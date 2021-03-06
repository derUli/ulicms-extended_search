<?php

class SearchController extends Controller
{

    public function truncateSearchIndex()
    {
        return Database::query("TRUNCATE `{prefix}fulltext`", true);
    }

    public function saveDataset($identifier, $url, $title, $content, $language)
    {
        $identifier = strval($identifier);
        $url = strval($url);
        $title = strval($title);
        $content = strval($content);
        // remove all characters that are unnecessary for search
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $content));
        $content = preg_replace('/\n(\s*\n)+/', "\n", $content); // Quotes are important here.
        $content = trim($content);
        
        $language = strval($language);
        $args = array(
            $identifier,
            $url,
            $title,
            $content,
            $language
        );
        
        $sql = "REPLACE INTO `{prefix}fulltext` (`identifier`, `url`, `title`, `content`, `language`) values (?, ?, ?, ?, ?)";
        return Database::pQuery($sql, $args, true);
    }

    public function runAllIndexers()
    {
        // iterate over all modules
        // run registered indexers of every module
        $modules = getAllModules();
        $this->truncateSearchIndex();
        foreach ($modules as $module) {
            $indexers = getModuleMeta($module, "indexers");
            if (! $indexers) {
                continue;
            }
            foreach ($indexers as $key => $value) {
                $fullPath = getModulePath($module, true) . $value;
                if (file_exists($fullPath)) {
                    include_once $fullPath;
                    if (class_exists($key)) {
                        $runner = new $key();
                        // Run method doIndex() of indexers
                        if ($runner instanceof Indexer and method_exists($runner, "doIndex")) {
                            $runner->doIndex();
                        }
                    }
                }
            }
        }
        // set timestamp of indexing time
        Settings::set("extended_search_last_index_build_date", time());
    }

    // Click on rebuild index now
    public function rebuildNowGet()
    {
        $this->runAllIndexers();
        Request::redirect(ModuleHelper::buildAdminURL("extended_search", "rebuild=1"));
    }

    // searchs for $subject in all index datasets with $language
    // returns an array of row objects
    public function search($subject, $language)
    {
        $subject = strval($subject);
        
        // It's possible to override search settings with CustomData
        // per page
        $customData = CustomData::get();
        $customData = isset($customData["extended_search"]) ? $customData["extended_search"] : array();
        
        $order = Settings::get("extended_search_order") ? Settings::get("extended_search_order") : "relevance";
        $sort_direction = Settings::get("extended_search_sort_direction") ? Settings::get("extended_search_sort_direction") : "desc";
        
        $order = isset($customData["order"]) ? $customData["order"] : $order;
        $sort_direction = isset($customData["sort_direction"]) ? $customData["sort_direction"] : $sort_direction;
        
        // sorting search results
        if (! in_array($order, array(
            "relevance",
            "title",
            "url"
        ))) {
            $order = "relevance";
        }
        
        // ascending or descending?
        if (! in_array($sort_direction, array(
            "asc",
            "desc"
        ))) {
            $sort_direction = "desc";
        }
        
        // query mysql fulltext index
        $sql = "SELECT *, MATCH (`content`) AGAINST (?) AS relevance
		FROM `{prefix}fulltext`
		WHERE MATCH (`content`) AGAINST (?) and language = ?
		ORDER BY $order $sort_direction";
        $args = array(
            $subject,
            $subject,
            $language
        );
        $query = Database::pQuery($sql, $args, true);
        $result = array();
        if (Database::getNumRows($query) > 0) {
            while ($row = Database::fetchObject($query)) {
                $result[] = $row;
            }
        }
        return $result;
    }
}
