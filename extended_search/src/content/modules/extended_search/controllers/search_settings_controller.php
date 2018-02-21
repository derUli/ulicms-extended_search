<?php

class SearchSettingsController extends MainClass
{

    public function settings()
    {
        return Template::executeModuleTemplate("extended_search", "settings.php");
    }

    public function getSettingsHeadline()
    {
        return get_translation("search_settings");
    }
}