<?php
use RTFLex\io\StreamReader;
use RTFLex\tokenizer\RTFTokenizer;
use RTFLex\tree\RTFDocument;

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
        $languages = function_exists("getAllUsedLanguages") ? getAllUsedLanguages() : getAllLanguages();
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
            case "rtf":
                $content = $this->rtfToText($file);
                break;
            case "pdf":
                $content = $this->pdfToText($file);
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
        $content = shell_exec($cmd);
        if (startsWith($content, "$file is not a Word Document.")) {
            $content = null;
        }
        return $content;
    }

    public function rtfToText($file)
    {
        $reader = new StreamReader($file);
        $tokenizer = new RTFTokenizer($reader);
        $doc = new RTFDocument($tokenizer);
        return $doc->extractText();
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