<?php

if ($box['context'] == 'action') {
	page_title (appconf ('news_name') . ' / ' . intl_get ('Authors'));
}

echo template_simple (
	'authors.spt',
	db_fetch_array ('select distinct author as name from sitellite_news where author is not null and author != "" order by author asc')
);

?>