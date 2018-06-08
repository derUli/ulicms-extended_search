<?php

class IXPatchDownloads extends Indexer
{

    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        if (! $controller) {
            return;
        }
        $versions = array();
        $path = Path::resolve("ULICMS_ROOT/patches/lists/*.txt");
        $files = glob($path);
        foreach ($files as $file) {
            $versions[] = pathinfo($file, PATHINFO_FILENAME);
        }
        usort($versions, "version_compare");
        
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            foreach ($versions as $version) {
					
                    $file = Path::resolve("ULICMS_ROOT/patches/lists/{$version}.txt");
                    
                    if (! file_exists($file)) {
                        throw new Exception("File not found");
                    }
                    $lines = StringHelper::linesFromFile($file);
                    $patches = array();
                    foreach ($lines as $line) {
                        $splitted = explode("|", $line);
                        $splitted = array_map('trim', $splitted);
                        $patch = new PatchDownload();
                        $patch->name = $splitted[0];
                        $patch->description = $splitted[1];
                        $patch->url = $splitted[2];
						
						$identifier = "{$language}/patch/" . md5($patch->url);
						$title = "Patch {$patch->name} for UliCMS {$version} - {$patch->description}";
						$content = implode(" ", array("Patch", "Patches", "Hotfix", "Hotfixes", "Bugfix", "Update",
						"download", "downloads",
						$patch->name, $patch->description, $patch->url));
						$url = $patch->url;
						$controller->saveDataset($identifier, $url, $title, $content, $language);
                    }
                }
        }
    }
}