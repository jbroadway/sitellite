<?php

/*
 * RSS Viewer Example
 *
 * This example shows how easy it is to display syndicated content from
 * another web site.
 */

if (empty ($parameters['newsfeed'])) {
	//echo 'no feed<br />';
	$parameters['newsfeed'] = 'http://www.mainsite.lo/index/news-rss-action';
}

if (empty ($parameters['expires'])) {
	$parameters['expires'] = 'auto';
}

loader_import ('saf.XML.RSS.Simple');

$rss = new SimpleRSS;
$doc = $rss->fetch ($parameters['newsfeed'], $parameters['expires']);
if (! $doc) {
	echo $rss->error;
	return;
}

$root = $doc->root->name;

$channel = array_shift ($doc->query ('/' . $root . '/channel'));
$channel = $channel->makeObj ();

// build a list of items
$items = $doc->query ('/' . $root . '/item');
if (! is_array ($items)) {
	$items = $doc->query ('/' . $root . '/channel/item');
	if (! is_array ($items)) {
		$items = array ();
	}
}
foreach ($items as $key => $item) {
	$items[$key] = $item->makeObj ();
}

$channel->items = $items;

if ($box['context'] == 'action') {
	page_title ($channel->title);
}

if ($parameters['limit'] > 0) {
	$channel->items = array_slice ($channel->items, 0, $parameters['limit']);
}

echo template_simple ('rss_viewer.spt', $channel);

?>