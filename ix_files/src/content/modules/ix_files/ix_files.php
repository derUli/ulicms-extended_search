<?php
class IXFiles extends Indexer {
	public function getIndexedFiletypes() {
		$extensions = array ();
		$query = Database::query ( "select extension from `{prefix}ix_files_types` order by extension", true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$extensions [] = $row->extension;
		}
		return $extensions;
	}
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$contentFolder = Path::resolve ( "ULICMS_DATA_STORAGE_ROOT/content/files" );
		$files = find_all_files ( $contentFolder );
		$languages = function_exists ( "getAllUsedLanguages" ) ? getAllUsedLanguages () : getAllLanguages ();
		$types = $this->getIndexedFiletypes ();
		foreach ( $files as $file ) {
			$content = $this->getFileContent ( $file, $types );
			if ($content) {
				$url = str_replace ( ULICMS_DATA_STORAGE_ROOT, '', $file );
				if (defined ( "ULICMS_DATA_STORAGE_URL" )) {
					$url = ULICMS_DATA_STORAGE_URL . $url;
				}
				foreach ( $languages as $language ) {
					$fulltext = $file . " " . basename ( $file ) . " " . pathinfo ( $file, PATHINFO_FILENAME ) . pathinfo ( $file, PATHINFO_FILENAME ) . " " . $content;
					$identifier = "file/" . $language . "/" . md5 ( $url );
					$title = basename ( $file );
					$controller->saveDataset ( $identifier, $url, $title, $fulltext, $language );
				}
			}
		}
	}
	protected function getFileContent($file, $types) {
		$content = null;
		$extension = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
		if ($types and ! in_array ( $extension, $types )) {
			return null;
		}
		switch ($extension) {
			case "doc" :
				$content = $this->docToText ( $file );
				break;
			case "docx" :
				$content = $this->docxToText ( $file );
				break;
			case "txt" :
				$content = file_get_contents ( $file );
				break;
			case "rtf" :
				$content = $this->rtfToText ( $file );
				break;
			case "pdf" :
				$content = $this->pdfToText ( $file );
				break;
			case "tex" :
				$content = $this->texToText ( $file );
				break;
			case "ps" :
				$content = $this->psToText ( $file );
				break;
			case "html" :
				$content = $this->htmlTotext ( $file );
				break;
			case "dvi" :
				$content = $this->dviToText ( $file );
				break;
			default :
				// default is null
				break;
		}
		
		if (is_string ( $content )) {
			$content = preg_replace ( '/\n(\s*\n)+/', "\n", $content ); // Quotes are important here.
			$content = preg_replace_callback ( '/&#([0-9a-fx]+);/mi', function ($ord) {
				return $this->replaceNumEntity ( $ord );
			}, $content );
			
			$content = trim ( $content );
		}
		return $content;
	}
	
	// index *.dvi files
	// see https://github.com/derUli/ulicms-extended_search/issues/50
	public function dviToText($file) {
		$pathToDviType = apply_filter ( '/usr/bin/catdvi', "path_to_catdvi" );
		$cmd = "$pathToDviType " . escapeshellarg ( $file );
		$content = shell_exec ( $cmd );
		return $content;
	}
	public function docToText($file) {
		$pathToAntiword = apply_filter ( "/usr/bin/antiword", "path_to_antiword" );
		$cmd = "$pathToAntiword " . escapeshellarg ( $file );
		$content = shell_exec ( $cmd );
		if (startsWith ( $content, "$file is not a Word Document." )) {
			$content = null;
		}
		return $content;
	}
	public function docxToText($file) {
		$pathToDoc2Txt = apply_filter ( "/usr/bin/docx2txt", "path_to_docx2txt" );
		$cmd = "$pathToDoc2Txt " . escapeshellarg ( $file ) . " -";
		$content = shell_exec ( $cmd );
		if (startsWith ( $content, "<" . $file . "> does not seem to be a docx file!" )) {
			$content = null;
		}
		return $content;
	}
	public function texToText($file) {
		$pathToDetex = apply_filter ( "/usr/bin/detex", "path_to_detex" );
		$cmd = "$pathToDetex " . escapeshellarg ( $file );
		$content = shell_exec ( $cmd );
		return $content;
	}
	public function psToText($file) {
		$pathToPsToText = apply_filter ( "/usr/bin/pstotext", "path_to_pstotext" );
		$cmd = "$pathToPsToText " . escapeshellarg ( $file );
		$content = shell_exec ( $cmd );
		return $content;
	}
	public function rtfToText($file) {
		return rtf2text ( $file );
	}
	public function pdfToText($file) {
		$converter = new PDF2Text ();
		$converter->setFilename ( $file );
		$converter->setUnicode ( false );
		$converter->decodePDF ();
		$content = $converter->output ();
		return $content;
	}
	public function htmlTotext($file) {
		$html = new \Html2Text\Html2Text ( file_get_contents ( $file ) );
		return $html->getText ();
	}
	private function replaceNumEntity($ord) {
		$ord = $ord [1];
		if (preg_match ( '/^x([0-9a-f]+)$/i', $ord, $match )) {
			$ord = hexdec ( $match [1] );
		} else {
			$ord = intval ( $ord );
		}
		
		$no_bytes = 0;
		$byte = array ();
		
		if ($ord < 128) {
			return chr ( $ord );
		} elseif ($ord < 2048) {
			$no_bytes = 2;
		} elseif ($ord < 65536) {
			$no_bytes = 3;
		} elseif ($ord < 1114112) {
			$no_bytes = 4;
		} else {
			return;
		}
		
		switch ($no_bytes) {
			case 2 :
				{
					$prefix = array (
							31,
							192 
					);
					break;
				}
			case 3 :
				{
					$prefix = array (
							15,
							224 
					);
					break;
				}
			case 4 :
				{
					$prefix = array (
							7,
							240 
					);
				}
		}
		
		for($i = 0; $i < $no_bytes; $i ++) {
			$byte [$no_bytes - $i - 1] = (($ord & (63 * pow ( 2, 6 * $i ))) / pow ( 2, 6 * $i )) & 63 | 128;
		}
		
		$byte [0] = ($byte [0] & $prefix [0]) | $prefix [1];
		
		$ret = '';
		for($i = 0; $i < $no_bytes; $i ++) {
			$ret .= chr ( $byte [$i] );
		}
		
		return $ret;
	}
}