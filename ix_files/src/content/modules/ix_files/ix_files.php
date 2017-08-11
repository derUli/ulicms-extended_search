<?php
class IXFiles extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$contentFolder = Path::resolve ( "ULICMS_ROOT/content" );
		$files = find_all_files ( $contentFolder );
		foreach ( $files as $file ) {
			$content = $this->getFileContent ( $file );
			if ($content) {
				$identifier = md5 ( $file, $file );
				$url = str_replace ( ULICMS_ROOT, '', $file );
				$title = basename ( $file );
				foreach ( getAllLanguages () as $language ) {
					$controller->saveDataset ( $identifier, $url, $title, $content, $language );
				}
			}
		}
	}
	protected function getFileContent($file) {
		$content = null;
		$extension = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
		switch ($extension) {
			case "doc" :
				$content = $this->pdfToText ( $file );
				break;
			case "txt" :
				$content = file_get_content_wrapper ( $file );
				break;
			default :
				// default is null
				break;
		}
		return $content;
	}
	public function pdfToText($file) {
		$pathToAntiword = apply_filter ( "/usr/bin/antiword", "path_to_antiword" );
		$cmd = "$pathToAntiword " . escapeshellarg ( $file );
		return shell_exec ( $cmd );
	}
}