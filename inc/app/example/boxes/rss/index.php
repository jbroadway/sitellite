<?php

/*
 * RSS Example
 *
 * This example shows how easy it is to create syndicated content for use in
 * another web site or portal.
 */

// construct an rss data structure
// this would be from a database or other resource in reality
$rss = array (
	'title' => 'My RSS Feed',
	'link' => site_url (),
	'description' => 'A demonstration of an RSS news feed.',
	'items' => array (
		array (
			'title' => 'Test Item 1',
			'link' => site_url () . site_prefix () . '/index/item1',
		),
		array (
			'title' => 'Test Item 2',
			'link' => site_url () . site_prefix () . '/index/item2',
		),
		array (
			'title' => 'Test Item 3',
			'link' => site_url () . site_prefix () . '/index/item3',
		),
	),
);

// in the conf/properties.php file of your app, you would also want to add
// the following code to add the rss feed to the page's head, so rss-aware
// browsers will see it.  in this context, since we exit without outputting
// the global template, this code does nothing.
page_add_link (
	'alternate',
	'application/rss+xml',
	'http://' . site_domain () . site_prefix () . '/index/example-rss-action'
);

// output the rss feed by setting the content type, displaying the rss
// template, and exiting so Sitellite doesn't render the output within
// the page body 
header ('Content-Type: text/xml');
echo template_simple ('rss.spt', $rss);
exit;

?>