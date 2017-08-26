<?php

class IXFilesModule extends Controller
{

    public function uninstall()
    {
        $migrator = new DBMigrator("indexer/ix_files", ModuleHelper::buildModuleRessourcePath("ix_files", "sql/down"));
        $migrator->rollback();
    }
}