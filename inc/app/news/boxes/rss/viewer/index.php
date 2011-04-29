<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #176 News - RSS viewer limit not working.
//

/*
 * RSS Viewer Example
 *
 * This example shows how easy it is to display syndicated content from
 * another web site.
 */

if (empty ($parameters['newsfeed'])) {
	$parameters['newsfeed'] = 'http://' . site_domain . site_prefix () . '/index/news-rss-action';
}

loader_import ('news.simplepie');

$feed = new SimplePie ($parameters['newsfeed']);

if (! empty ($parameters['expires'])) {
	$feed->set_cache_duration ($parameters['expires']);
}

if (! empty ($parameters['limit'])) {
	$feed->set_item_limit ($parameters['limit']);
}

$feed->handle_content_type ();

if ($box['context'] == 'action') {
	page_title ($feed->get_title ());
}
// Start: SEMIAS #176 News - RSS viewer limit not working.
$feed->limit = $parameters['limit'];
// END: SEMIAS
echo template_simple ('rss_viewer2.spt', $feed);

/*if (empty ($parameters['newsfeed'])) {
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

echo template_simple ('rss_viewer.spt', $channel);*/

?>