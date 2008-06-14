<?php

if (@file_exists ('inc/app/sitesearch/data/sitesearch.pid')) {
	header ('Location: ' . site_prefix () . '/index/sitesearch-app?ctype=sitellite_news&show_types=yes');
	exit;
}

if ($box['context'] == 'action') {
	page_title (intl_get ('News Search'));
}

if (! $parameters['query']) {
	echo template_simple ('search.spt', $parameters);
	return;
}

loader_import ('news.Functions');
loader_import ('news.Story');

$story = new NewsStory;

if (! $parameters['limit']) {
	$parameters['limit'] = 10;
}

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

$story->limit ($parameters['limit']);
$story->offset ($parameters['offset']);

loader_import ('help.Help');

$params = array ();

foreach (help_split_query ($parameters['query']) as $item) {
	$q = db_quote ('%' . $item . '%');
	$params[] = 'title like ' . $q . ' or
		summary like ' . $q . ' or
		body like ' . $q;
}

$parameters['results'] = $story->find ($params);

$parameters['total'] = $story->total;

loader_import ('saf.GUI.Pager');

$pg = new Pager ($parameters['offset'], $parameters['limit'], $parameters['total']);
$pg->getInfo ();
$pg->setUrl (site_prefix () . '/index/news-search-action?query=' . urlencode ($parameters['query']));

template_simple_register ('pager', $pg);
echo template_simple ('search.spt', $parameters);

?>
