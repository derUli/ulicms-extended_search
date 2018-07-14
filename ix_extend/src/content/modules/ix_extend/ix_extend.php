<?php
class IXExtend extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}
		$args = array (
				1,
				"all",
				"article" 
		);
		$sql = "Select id, title, systemname, language, alternate_title, content, excerpt, meta_keywords, 
		meta_description, article_image, og_image from `{prefix}content` where active = ? and access = ? and `type` = ?";
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$identifier = "extension/" . strval ( $row->id );
			if (StringHelper::isNotNullOrEmpty ( $row->alternate_title )) {
				$title = $row->alternate_title;
			} else {
				$title = $row->title;
			}
			$language = $row->language;
			$url = $row->systemname . ".html";
			$datas = array (
					$row->systemname,
					$row->content,
					$row->excerpt,
					$row->alternate_title,
					$row->title,
					$row->meta_description,
					$row->meta_keywords, 
					$row->article_image 
			);
			
			$cdata = CustomData::get ( $row->systemname );
			if (isset ( $cdata ["manufacturer"] ) and StringHelper::isNotNullOrEmpty ( $cdata ["manufacturer"] )) {
				$datas [] = $cdata ["manufacturer"];
			}
			$datas = array_filter ( $datas, "strlen" );
			$content = implode ( " ", $datas );
			$content = strip_tags ( $content );
			$content = unhtmlspecialchars ( $content );
			$controller->saveDataset ( $identifier, $url, $title, $content, $language );
		}
    Database::query("update {prefix}content set og_image = article_image where article_image is not null and article_image <> '' and (og_image is null or og_image = '')", true);
	}
}