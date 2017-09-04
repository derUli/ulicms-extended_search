<?php

class IXFiles extends Indexer
{

    public function getIndexedFiletypes()
    {
        $extensions = array();
        $query = Database::query("select extension from `{prefix}ix_files_types` order by extension", true);
        while ($row = Database::fetchObject($query)) {
            $extensions[] = $row->extension;
        }
        return $extensions;
    }

    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        if (! $controller) {
            return;
        }
        $contentFolder = Path::resolve("ULICMS_ROOT/content/files");
        $files = find_all_files($contentFolder);
        $languages = function_exists("getAllUsedLanguages") ? getAllUsedLanguages() : getAllLanguages();
        $types = $this->getIndexedFiletypes();
        foreach ($files as $file) {
            $content = $this->getFileContent($file, $types);
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

    protected function getFileContent($file, $types)
    {
        $content = null;
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($types and ! in_array($extension, $types)) {
            return null;
        }
        switch ($extension) {
            case "doc":
                $content = $this->docToText($file);
                break;
            case "docx":
                $content = $this->docxToText($file);
                break;
            case "txt":
                $content = file_get_contents($file);
                break;
            case "rtf":
                $content = $this->rtfToText($file);
                break;
            case "pdf":
                $content = $this->pdfToText($file);
                break;
            case "tex":
                $content = $this->texToText($file);
                break;
            case "ps":
                $content = $this->psToText($file);
                break;
            default:
                // default is null
                break;
        }
        
        if (is_string($content)) {
            $content = preg_replace('/\n(\s*\n)+/', "\n", $content); // Quotes are important here.
            $content = trim($content);
        }
        return $content;
    }

    public function docToText($file)
    {
        $pathToAntiword = apply_filter("/usr/bin/antiword", "path_to_antiword");
        $cmd = "$pathToAntiword " . escapeshellarg($file);
        $content = shell_exec($cmd);
        if (startsWith($content, "$file is not a Word Document.")) {
            $content = null;
        }
        return $content;
    }

    public function docxToText($file)
    {
        $pathToDoc2Txt = apply_filter("/usr/bin/docx2txt", "path_to_docx2txt");
        $cmd = "$pathToDoc2Txt " . escapeshellarg($file) . " -";
        $content = shell_exec($cmd);
        if (startsWith($content, "<" . $file . "> does not seem to be a docx file!")) {
            $content = null;
        }
        return $content;
    }

    public function texToText($file)
    {
        $pathToDetex = apply_filter("/usr/bin/detex", "path_to_detex");
        $cmd = "$pathToDetex " . escapeshellarg($file);
        $content = shell_exec($cmd);
        return $content;
    }

    public function psToText($file)
    {
        $pathToPsToText = apply_filter("/usr/bin/pstotext", "path_to_pstotext");
        $cmd = "$pathToPsToText " . escapeshellarg($file);
        $content = shell_exec($cmd);
        return $content;
    }

    public function rtfToText($file)
    {
        return rtf2text($file);
    }

    public function pdfToText($file)
    {
        $converter = new PDF2Text();
        $converter->setFilename($file);
        $converter->setUnicode(false);
        $converter->decodePDF();
        $content = $converter->output();
        return $content;
    }
}