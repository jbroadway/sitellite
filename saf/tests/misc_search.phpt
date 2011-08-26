--TEST--
saf.Misc.Search
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Search');

// search_split_query() function

var_dump (search_split_query ('$query'));

// search_highlight() function

var_dump (search_highlight ('$string', '$queries'));

// search_bar() function

var_dump (search_bar ('$query', '$url'));

// get_searchengine_keywords() function

var_dump (get_searchengine_keywords ('$referer'));

// saf_misc_search_content_filter() function

var_dump (saf_misc_search_content_filter ('$body'));

?>
--EXPECT--
