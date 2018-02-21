<?php
$controller = ControllerRegistry::get("SearchController");
if ($controller and Settings::get("extended_search_rebuild_index_on_clear_cache")) {
    $controller->runAllIndexers();
}