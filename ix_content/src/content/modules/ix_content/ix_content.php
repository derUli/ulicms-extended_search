<?php
class IXContent extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$args = array (
				1,
				"all",
				"node",
				"#" 
		);
		$sql = "Select id, title, systemname, language, alternate_title, content, excerpt, meta_keywords, meta_description from `{prefix}content` where active = ? and access = ? and `type` <> ? and `redirection` <> ?";
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$identifier = "content/" . strval ( $row->id );
			if (isNotNullOrEmpty ( $row->alternate_title )) {
				$title = $row->alternate_title;
			} else {
				$title = $row->title;
			}
			$language = $row->language;
			$url = $row->systemname . ".html";
			$datas = array (
					$row->content,
					$row->excerpt,
					$row->alternate_title,
					$row->title,
					$row->meta_description,
					$row->meta_keywords,
					$row->redirection,
					$row->custom_data 
			);
			
			$sql = "Select value from {prefix}custom_fields where content_id = ?";
			$args = array (
					$row->id 
			);
			$query2 = Database::pQuery ( $sql, $args, true );
			while ( $row2 = Database::fetchobject ( $query2 ) ) {
				$datas [] = $row2->value;
			}
			
			$datas = array_filter ( $datas, "strlen" );
			$content = implode ( " ", $datas );
			$content = strip_tags ( $content );
			$content = unhtmlspecialchars ( $content );
			$content = trim ( $content );
			$controller->saveDataset ( $identifier, $url, $title, $content, $language );
		}
	}
}