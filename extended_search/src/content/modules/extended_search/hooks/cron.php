<?php
// rebuild fulltext index daily if it's enabled
if (ControllerRegistry::get("SearchController") and Settings::get("extended_search_rebuld_index_cron")) {
    BetterCron::days("module/extended_search/rebuild_index", 1, function () {
        $controller = ControllerRegistry::get("SearchController");
        $controller->runAllIndexers();
    });
}