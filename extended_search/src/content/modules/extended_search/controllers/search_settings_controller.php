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
        }
        Request::redirect(ModuleHelper::buildAdminURL("extended_search", "save=1"));
    }

    public function getSettingsHeadline()
    {
        return get_translation("search_settings");
    }
}