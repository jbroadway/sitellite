<?php

/*
 * RSS Viewer Example
 *
 * This example shows how easy it is to display syndicated content from
 * another web site.
 *
 * Please note: for a more full-featured RSS viewer, please use the box
 * news/rss/viewer instead of building on this.  It already features
 * automatic caching and other handy features.
 */

// set our resource to our example rss feed
$resource = site_url () . site_prefix () . '/index/example-rss-action';

// retrieve rss feed
$rssfeed = join ('', file ($resource));

// parse the rss feed contents
loader_import ('saf.XML.Sloppy');
$sloppy = new SloppyDOM;

$doc = $sloppy->parse ($rssfeed);
if (! $doc) {
	echo $sloppy->error;
	return;
}

$channel = array_shift ($doc->query ('/rdf:RDF/channel'));
$channel = $channel->makeObj ();

// build a list of items
$items = $doc->query ('/rdf:RDF/item');
foreach ($items as $key => $item) {
	$items[$key] = $item->makeObj ();
}

$channel->items = $items;

page_title ($channel->title);
echo template_simple ('rss_viewer.spt', $channel);

?>