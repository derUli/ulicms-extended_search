<?php
$migrator = new DBMigrator("indexer/ix_files", ModuleHelper::buildModuleRessourcePath("ix_files", "sql/up"));
$migrator->migrate();