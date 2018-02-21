<?php
Database::query("DROP TABLE `{prefix}fulltext`", true);
Settings::delete("extended_search_last_index_build_date");
