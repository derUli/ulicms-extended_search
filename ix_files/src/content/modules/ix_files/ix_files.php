<?php

class IXFiles extends Indexer
{

    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        if (! $controller) {
            return;
        }
        $contentFolder = Path::resolve("ULICMS_ROOT/content/files");
        $files = find_all_files($contentFolder);
        $languages = getAllLanguages();
        foreach ($files as $file) {
            $content = $this->getFileContent($file);
            if ($content) {
                $url = str_replace(ULICMS_ROOT, '', $file);
                foreach ($languages as $language) {
                    $fulltext = $file . " " . basename($file) . " " . pathinfo($file, PATHINFO_FILENAME) . pathinfo($file, PATHINFO_FILENAME) . " " . $content;
                    $identifier = "file/" . $language . "/" . md5($url);
                    $title = basename($file);
                    $controller->saveDataset($identifier, $url, $title, $fulltext, $language);
                }
            }
        }
    }

    protected function getFileContent($file)
    {
        $content = null;
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($extension) {
            case "doc":
                $content = $this->docToText($file);
                break;
            case "docx":
                // @TODO: Implented docx indexing
                break;
            case "txt":
                $content = file_get_contents($file);
                break;
            case "pdf":
                // @TODO: Implented pdf indexing
                break;
            default:
                // default is null
                break;
        }
        return $content;
    }

    public function docToText($file)
    {
        $pathToAntiword = apply_filter("/usr/bin/antiword", "path_to_antiword");
        $cmd = "$pathToAntiword " . escapeshellarg($file);
        return shell_exec($cmd);
    }
}