<?php

class SearchSettingsController extends MainClass
{

    public function settings()
    {
        return Template::executeModuleTemplate("extended_search", "settings.php");
    }

    public function savePost()
    {
        $acl = new ACL();
        if ($acl->hasPermission("search_settings_change")) {
            $orderOptions = array(
                "relevance",
                "title",
                "url"
            );
            
            $sortDirections = array(
                "asc",
                "desc"
            );
            
            if (in_array(Request::getVar("extended_search_order"), $orderOptions)) {
                Settings::set("extended_search_order", Request::getVar("extended_search_order"));
            }
            if (in_array(Request::getVar("extended_search_sort_direction"), $sortDirections)) {
                Settings::set("extended_search_sort_direction", Request::getVar("extended_search_sort_direction"));
            }
            if (Request::getVar("extended_search_rebuild_index_on_clear_cache")) {
                Settings::set("extended_search_rebuild_index_on_clear_cache", "1");
            } else {
                Settings::delete("extended_search_rebuild_index_on_clear_cache");
            }
            if (Request::getVar("extended_search_rebuld_index_cron")) {
                Settings::set("extended_search_rebuld_index_cron", "1");
            } else {
                Settings::delete("extended_search_rebuld_index_cron");
            }
        }
        Request::redirect(ModuleHelper::buildAdminURL("extended_search", "save=1"));
    }

    public function getSettingsHeadline()
    {
        return get_translation("search_settings");
    }
}