<?php
class IXBlog extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$allPages = getAllPages ();
		$blogPages = array ();
		foreach ( $allPages as $page ) {
			if (containsModule ( $page ["systemname"], "blog" )) {
				$blogPages [$page ["language"]] = $page;
			}
		}
		if (count ( $blogPages ) == 0) {
			return;
		}
		foreach ( $blogPages as $language => $page ) {
			$args = array (
					1,
					$language 
			);
			$sql = "Select id, content_full, content_preview, title, seo_shortname, language from {prefix}blog where entry_enabled = ? and language = ?";
			$query = Database::pQuery ( $sql, $args, true );
			while ( $row = Database::fetchObject ( $query ) ) {
				$identifier = "blog/" . strval ( $row->id );
				$title = $row->title;
				$language = $row->language;
				$url = $page ["systemname"] . ".html?single=" . $row->systemname;
				$datas = array (
						$row->title,
						$row->content_full,
						$row->content_preview,
						$row->seo_shortname 
				);
				$datas = array_filter ( $datas, "strlen" );
				$content = implode ( " ", $datas );
				$content = strip_tags ( $content );
				$content = unhtmlspecialchars ( $content );
				$controller->saveDataset ( $identifier, $url, $title, $content, $language );
			}
		}
	}
}