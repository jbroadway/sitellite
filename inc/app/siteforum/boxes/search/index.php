<?php

if (@file_exists ('inc/app/sitesearch/data/sitesearch.pid')) {
	header ('Location: ' . site_prefix () . '/index/sitesearch-app?ctype=siteforum_post&show_types=yes');
	exit;
}

if ($box['context'] == 'action') {
	page_title (intl_get ('Forums Search'));
}

if (! $parameters['query']) {
	echo template_simple ('search.spt', $parameters);
	return;
}

loader_import ('siteforum.Post');
loader_import ('siteforum.Topic');
loader_import ('siteforum.Filters');
loader_import ('saf.GUI.Pager');

$p = new SiteForum_Post;

if (! isset ($parameters['limit'])) {
	$parameters['limit'] = 10;
}

if (! isset ($parameters['offset'])) {
	$parameters['offset'] = 0;
}

$p->limit ($parameters['limit']);
$p->offset ($parameters['offset']);

$url = site_prefix () . '/index/siteforum-search-action?query=' . urlencode ($parameters['query']);
$t = new SiteForum_Topic ();
if ($parameters['topic']) {
	$url .= '&topic=' . $parameters['topic'];
	$parameters['topic_name'] = $t->getTitle ($parameters['topic']);
}
else {
	$parameters['topic'] = null;
}
if ($parameters['post']) {
	$url .= '&post=' . $parameters['post'];
}
else {
	$parameters['post'] = null;
}

loader_import ('help.Help');

$params = array ();

foreach (help_split_query ($parameters['query']) as $item) {
	$q = db_quote ('%' . $item . '%');
	$q2 = db_quote ('%' . htmlentities ($item, ENT_NOQUOTES, 'utf-8') . '%');
	$par = '(subject like ' . $q . ' or body like ' . $q2 . ')';
	if (! is_null ($parameters['topic'])) {
		$par .= ' and topic_id = ' . $parameters['topic'];
	}
/*
	if (! is_null ($parameters['post'])) {
		$par .= ' and post_id = ' . $parameters['post'];
	}
*/
	$params[] = $par;
}

$parameters['results'] = $p->find ($params);
$parameters['total'] = $p->total;

if (! $parameters['topic']) {
foreach ($parameters['results'] as $k=>$r) {
	$parameters['results'][$k]->topic = $t->getTitle ($r->topic_id);
}
}

$pg = new Pager ($parameters['offset'], $parameters['limit'], $parameters['total']);
$pg->getInfo ();
$pg->setUrl ($url);

template_simple_register ('pager', $pg);
echo template_simple ('search.spt', $parameters);

?>
