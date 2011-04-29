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
// #177 Pagination.
// #192 Test all config files for multilingual dates.

loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

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
//START: SEMIAS. #192 Test all config files for multilingual dates.
$story->date = intl_date ($story->date, 'shortcevdate');
//END: SEMIAS.
$story->limit ($parameters['limit']);
$story->offset ($parameters['offset']);

loader_import ('help.Help');

$params = array ();

foreach (help_split_query ($parameters['query']) as $item) {
	$q = db_quote ('%' . $item . '%');
	$params[] = '(title like ' . $q . ' or
		summary like ' . $q . ' or
		body like ' . $q . ')';
}

$parameters['results'] = $story->find ($params);

$parameters['total'] = $story->total;

loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.	
$pg = new Pager ($parameters['offset'], $parameters['limit'], $parameters['total']);
$pg->getInfo ();
$pg->setUrl (site_prefix () . '/index/news-search-action?query=' . urlencode ($parameters['query']));

template_simple_register ('pager', $pg);
// END: SEMIAS
echo template_simple ('search.spt', $parameters);

?>
