<?php

class IXBlog extends Indexer
{

    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        if (! $controller) {
            return;
        }
        $allPages = getAllPages();
        $blogPages = array();
        // get the pages in any language which contains the blog module
        foreach ($allPages as $page) {
            if (containsModule($page["slug"], "blog")) {
                $blogPages[$page["language"]] = $page;
            }
        }
        // If there is no blog page we stop here.
        if (count($blogPages) == 0) {
            return;
        }
        foreach ($blogPages as $language => $page) {
            $args = array(
                1,
                $language
            );
            // select all blog articles in the current language
            $sql = "Select id, content_full, content_preview, title, seo_shortname,
			meta_description, meta_keywords, language from {prefix}blog where entry_enabled = ? and language = ?";
            $query = Database::pQuery($sql, $args, true);
            while ($row = Database::fetchObject($query)) {
                $identifier = "blog/" . strval($row->id);
                $title = $row->title;
                $language = $row->language;
                $url = $page["slug"] . ".html?single=" . $row->seo_shortname;
                // collect all blog attributes
                $datas = array(
                    $row->title,
                    $row->content_full,
                    $row->content_preview,
                    $row->seo_shortname,
                    $row->meta_description,
                    $row->meta_keywords
                
                );
                // query comments for the current blog article
                $sql = "Select name, url, comment from {prefix}blog_comments where post_id = ?";
                $args = array(
                    $row->id
                );
                $queryComments = Database::pQuery($sql, $args, true);
                // add comment texts to the data array
                while ($row = Database::fetchObject($queryComments)) {
                    $datas[] = $row->name;
                    $datas[] = $row->url;
                    $datas[] = $row->comment;
                }
                
                $datas = array_filter($datas, "strlen");
                // join all content data with whitespace
                $content = implode(" ", $datas);
                // remove all html tags
                $content = strip_tags($content);
                // decode htmlspecialchars
                $content = unhtmlspecialchars($content);
                $controller->saveDataset($identifier, $url, $title, $content, $language);
            }
        }
    }
}
