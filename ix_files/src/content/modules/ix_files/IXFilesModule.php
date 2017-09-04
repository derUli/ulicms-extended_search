<?php

class IXFilesModule extends Controller
{

    private $moduleName = "ix_files";

    public function uninstall()
    {
        $migrator = new DBMigrator("indexer/ix_files", ModuleHelper::buildModuleRessourcePath("ix_files", "sql/down"));
        $migrator->rollback();
    }

    private function setExtensions($extensions)
    {
        Database::truncateTable("ix_files_types");
        foreach ($extensions as $extension) {
            Database::pQuery("INSERT INTO `{prefix}ix_files_types` (extension) values(?)", array(
                $extension
            ), true);
        }
    }

    public function getAllSupportedTypes()
    {
        $types = array(
            "pdf",
            "doc",
            "docx",
            "txt",
            "rtf",
            "tex"
        );
        sort($types);
        return $types;
    }

    public function getIndexedFiletypes()
    {
        $extensions = array();
        $query = Database::query("select extension from `{prefix}ix_files_types` order by extension", true);
        while ($row = Database::fetchObject($query)) {
            $extensions[] = $row->extension;
        }
        return $extensions;
    }

    public function settings()
    {
        if (Request::isPost() and Request::hasVar("extensions") and is_array(Request::getVar("extensions"))) {
            $extensions = Request::getVar("extensions");
            $this->setExtensions($extensions);
        }
        return Template::executeModuleTemplate($this->moduleName, "settings.php");
    }
}
