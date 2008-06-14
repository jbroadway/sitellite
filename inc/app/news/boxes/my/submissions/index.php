<?php

if (! isset ($parameters['status'])) {
	$parameters['status'] = 'approved';
	$status = 'approved';
} elseif ($parameters['status'] == 'pending') {
	$status = 'draft';
} else {
	$status = $parameters['status'];
}

if ($parameters['status'] == 'approved') {
	$url = site_prefix () . '/index/news-app/story.';
} else {
	$url = site_prefix () . '/index/news-my-story-action/story.';
}

if (! isset ($parameters['user'])) {
	$parameters['user'] = session_username ();
}

$res = db_fetch_array (
	'select id, title, date, category, summary from sitellite_news where author = ? and sitellite_status = ? order by date desc, time desc',
	$parameters['user'],
	$status
);

if ($box['context'] == 'action') {
	page_title (ucfirst ($parameters['status']) . ' ' . intl_get ('News Stories') . ' (' . count ($res) . ')');
}

loader_import ('news.Functions');

echo template_simple (
	'my_submissions.spt',
	array (
		'list' => $res,
		'url' => $url,
	)
);

?>