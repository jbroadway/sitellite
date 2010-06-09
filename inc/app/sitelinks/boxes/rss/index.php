<?php

global $cgi;

loader_import ('sitelinks.Item');
loader_import ('sitelinks.Filters');
loader_import ('sitelinks.Functions');

$item = new SiteLinks_Item;

global $cgi;

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

if (! isset ($cgi->limit)) {
	$cgi->limit = 10;
}

if ($parameters['display'] == 'newest') {
	$parameters['category'] = intl_get ('Newest Links');
} elseif ($parameters['display'] == 'top') {
	$parameters['category'] = intl_get ('Top-Rated Links');
}

if ($parameters['category'] == intl_get ('Newest Links')) {
	$list = $item->getNewest ($cgi->limit);
	$title = appconf ('rss_title') . ': ' . intl_get ('Newest Links');
} elseif ($parameters['category'] == intl_get ('Top-Rated Links')) {
	$list = $item->getTop ($cgi->limit);
	$title = appconf ('rss_title') . ': ' . intl_get ('Top-Rated Links');
} else {
	$list = $item->getCategory ($parameters['category'], $cgi->limit, $cgi->offset);
	$title = appconf ('rss_title') . ': ' . $parameters['category'];
}

header ('Content-Type: text/xml');
echo template_simple (
	'rss_category.spt',
	array (
		'list' => $list,
		'date' => $parameters['date'],
		'rss_title' => $title,
		'rss_description' => appconf ('rss_description'),
		'rss_date' => date ('Y-m-d\TH:i:s') . sitelinks_timezone (date ('Z')),
	)
);

exit;

?>