<?php

class IXFullCalendar extends Indexer
{

    // indexes all fullcalendar events with urls
    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        if (! $controller) {
            return;
        }
        
        // select all events with link urls from fullcalendar
        $query = Database::query("select id, title, url from `{prefix}events` where url is not null and url <> '' and url <> 'http://' and url <> 'https://'", true);
        while ($row = Database::fetchObject($query)) {
            
            $url = $row->url;
            $title = $row->title;
            $content = "{$row->title} {$row->url}";
            $languages = getAllUsedLanguages();
            // since events are not language dependent
            // add a dataset to index for every language
            foreach ($languages as $language) {
                $identifier = "events/{$row->id}/{$language}";
                $controller->saveDataset($identifier, $url, $title, $content, $language);
            }
        }
    }
}
