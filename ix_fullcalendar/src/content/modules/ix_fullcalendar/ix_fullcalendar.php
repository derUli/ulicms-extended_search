<?php
class IXFullCalendar extends Indexer {
	public function doIndex() {
		$controller = ControllerRegistry::get ( "SearchController" );
		if (! $controller) {
			return;
		}

  $query = Database::query("select id, title, url from `{prefix}events` where url is not null and url <> '' and url <> 'http://' and url <> 'https://'", true);
	while($row = Database::fetchObject($query)){

		$url = $row->url;
		$title = $row->title;
		$content = "{$row->title} {$row->url}";
		$languages = getAllUsedLanguages();
		foreach($languages as $language){
			$identifier = "events/{$row->id}/{$language}";
		   $controller->saveDataset ( $identifier, $url, $title, $content, $language );
		}
	}

		}
	}
