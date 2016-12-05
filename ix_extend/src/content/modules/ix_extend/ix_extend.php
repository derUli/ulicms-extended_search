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
		$sql = "Select id, title, systemname, language, headline, content, excerpt from `{prefix}content` where active = ? and access = ? and `type` = ?";
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $sql ) ) {
			$identifier = "extension/" . strval ( $row->id );
			if (isNotNullOrEmpty ( $row->headline )) {
				$title = $row->headline;
			} else {
				$title = $row->title;
			}
			$language = $row->language;
			$url = buildSEOUrl ( $row->systemname );
			$datas = array (
					$row->content,
					$row->excerpt,
					$row->headline,
					$row->title 
			);
			
			$cdata = CustomData::get ( $row->systemname );
			if (isset ( $cdata ["manufacturer"] ) and isNotNullOrEmpty ( $cdata ["manufacturer"] )) {
				$datas [] = $cdata ["manufacturer"];
			}
			$datas = array_filter ( $datas, "empty" );
			$content = implode ( " ", $datas );
			$controller->saveDataset ( $identifier, $url, $title, $content, $language );
		}
	}
}