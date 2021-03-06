<?php
Database::query("CREATE TABLE `{prefix}fulltext` ( `identifier` VARCHAR(100) NOT NULL, `url` VARCHAR(255) NOT NULL , `title` VARCHAR(255) NOT NULL , `content` MEDIUMTEXT NOT NULL , `language` VARCHAR(100) NOT NULL , PRIMARY KEY (`identifier`) , FULLTEXT `ft_content` (`content`) ) ENGINE = MyISAM DEFAULT CHARSET=utf8;", true);

Settings::register("extended_search_order", "relevance");
Settings::register("extended_search_sort_direction", "desc");