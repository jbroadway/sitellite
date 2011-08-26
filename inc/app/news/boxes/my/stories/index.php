<?php

$url = site_prefix () . '/index/news-app/story.';

if (! isset ($parameters['user'])) {
	$parameters['user'] = session_username ();
}

$res = db_fetch_array (
	'select id, title, date, category, summary from sitellite_news where author = ? and sitellite_status = ? order by date desc, time desc',
	$parameters['user'],
	'approved'
);

if ($box['context'] == 'action') {
	page_title (intl_get ('News Stories') . ' / ' . intl_get ('By') . $parameters['user']);
}

loader_import ('news.Functions');

echo template_simple (
	'my_stories.spt',
	array (
		'list' => $res,
		'url' => $url,
	)
);

?>