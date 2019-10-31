<?php

class IXContent extends Indexer
{

    public function doIndex()
    {
        $controller = ControllerRegistry::get("SearchController");
        // if the SearchController is null there is something really wrong.
        // this should never happen.
        if (!$controller) {
            return;
        }
        $args = array(
            1,
            "all",
            "node"
        );
        // Query content table
        $sql = "Select id, title, slug, language, alternate_title, content, excerpt, meta_keywords, meta_description, article_author_name, article_author_email, link_url 
		from `{prefix}content` where active = ? and access = ? and `type` <> ? AND `deleted_at` IS NULL";
        $query = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($query)) {
            // every index entry must have an unique identifier string
            $identifier = "content/" . strval($row->id);
            if (StringHelper::isNotNullOrEmpty($row->alternate_title)) {
                $title = $row->alternate_title;
            } else {
                $title = $row->title;
            }
            $language = $row->language;
            $url = $row->slug . ".html";
            $datas = array(
                $row->slug,
                $row->content,
                $row->excerpt,
                $row->alternate_title,
                $row->title,
                $row->meta_description,
                $row->meta_keywords,
                $row->link_url,
                $row->custom_data,
                $row->article_author_name,
                $row->article_author_email
            );
            
            // Add custom field values to search index
            $sql = "Select value from {prefix}custom_fields where content_id = ?";
            $args = array(
                $row->id
            );
            $query2 = Database::pQuery($sql, $args, true);
            while ($row2 = Database::fetchobject($query2)) {
                $datas[] = $row2->value;
            }
            
            // indexes frequently asked questions if the "faq" module is installed
            if (containsModule($row->slug, "faq")) {
                $sql = "select * from {prefix}faq order by id";
                $query3 = Database::query($sql, true);
                while ($row3 = Database::fetchObject($query3)) {
                    $datas[] = $row3->question;
                    $datas[] = $row3->answer;
                }
            }
            // join all search data seperated by whitespace
            $content = implode(" ", $datas);
            // remove all html tags
            $content = strip_tags($content);
            // decode htmlspecialchars
            $content = unhtmlspecialchars($content);
            $content = trim($content);

            // save index entry in database
            $controller->saveDataset($identifier, $url, $title, $content, $language);
        }
    }
}