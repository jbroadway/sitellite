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
// #192 Test all config files for multilingual dates.

global $cgi;

loader_import ('sitelinks.Item');
loader_import ('sitelinks.Filters');
loader_import ('sitelinks.Functions');
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

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
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//header ('Content-Type: text/xml');
//echo template_simple (
//	'rss_category.spt',
//	array (
//		'list' => $list,
//		'date' => $parameters['date'],
//		'rss_title' => $title,
//		'rss_description' => appconf ('rss_description'),
//		'rss_date' => date ('Y-m-d\TH:i:s') . sitelinks_timezone (date ('Z')),
//	)
//);
//-----------------------------------------------
header ('Content-Type: text/xml');
echo template_simple (
	'rss_category.spt',
	array (
		'list' => $list,
		'date' => intl_date ($parameters['date'], 'shortcevdate'),
		'rss_title' => $title,
		'rss_description' => appconf ('rss_description'),
		'rss_date' => intl_date (date ('Y-m-d'), 'shortcevdate'),
	)
);
//END: SEMIAS.
exit;

?>