<?php

if ($box['context'] == 'action') {
	page_title (appconf ('news_name') . ' / ' . intl_get ('Sections'));
}

$res = db_fetch_array ('select category as name, count(*) as total from sitellite_news where category is not null and category != "" group by category order by category asc');

echo template_simple (
	'sections.spt',
	array (
		'list' => $res,
		'menu' => $parameters['menu'],
	)
);

?>
