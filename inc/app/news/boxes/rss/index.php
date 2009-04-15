<?php

global $cgi;

loader_import ('news.Story');
loader_import ('news.Functions');

$story = new NewsStory;

if (! empty ($parameters['section'])) { // view section list

	if (! $parameters['limit']) {
		$parameters['limit'] = 10;
	}
	if (! $parameters['offset']) {
		$parameters['offset'] = 0;
	}
	$story->limit ($parameters['limit']);
	$story->offset ($parameters['offset']);
	$story->orderBy ('date desc, rank desc, id desc');
	$list = $story->find (array ('category' => $parameters['section']));
	if (! $list) {
		$list = array ();
	}

	$date = false;
	$newlist = array ();
	foreach ($list as $item) {
		if ($date != $item->date) {
			$date = $item->date;
			$i = new StdClass;
			$i->_type = 'date';
			$i->date = $item->date;
			$newlist[] = $i;
		}
		$newlist[] = $item;
	}
	$list = $newlist;

	header ('Content-Type: text/xml; charset=' . intl_charset ());
	echo template_simple (
		'rss_section.spt',
		array (
			'list' => $list,
			'rss_title' => appconf ('rss_title'),
			'rss_description' => appconf ('rss_description'),
			'rss_date' => date ('Y-m-d\TH:i:s') . news_timezone (date ('Z')),
		)
	);

} elseif (! empty ($parameters['author'])) { // view all by author

	if (! $parameters['limit']) {
		$parameters['limit'] = 10;
	}
	if (! $parameters['offset']) {
		$parameters['offset'] = 0;
	}
	$story->limit ($parameters['limit']);
	$story->offset ($parameters['offset']);
	$story->orderBy ('date desc, rank desc, id desc');
	$list = $story->find (array ('author' => $parameters['author']));
	if (! $list) {
		$list = array ();
	}

	$total = $story->total;

	$date = false;
	$newlist = array ();
	foreach ($list as $item) {
		if ($date != $item->date) {
			$date = $item->date;
			$i = new StdClass;
			$i->_type = 'date';
			$i->date = $item->date;
			$newlist[] = $i;
		}
		$newlist[] = $item;
	}
	$list = $newlist;

	header ('Content-Type: text/xml; charset=' . intl_charset ());
	echo template_simple (
		'rss_author.spt',
		array (
			'list' => $list,
			'rss_title' => appconf ('rss_title'),
			'rss_description' => appconf ('rss_description'),
			'rss_date' => date ('Y-m-d\TH:i:s') . news_timezone (date ('Z')),
		)
	);

} else { // main list

	if (! $parameters['limit']) {
		$parameters['limit'] = 5;
	}
	$story->limit ($parameters['limit']);
	$story->orderBy ('date desc, rank desc, id desc');

	if (! isset ($parameters['sec'])) {
		$params = array ();
	} else {
		$params = array ('category' => $parameters['sec']);
	}

	$list = $story->find ($params);
	if (! $list) {
		$list = array ();
	}

	header ('Content-Type: text/xml; charset=' . intl_charset ());
	echo template_simple (
		'rss_frontpage.spt',
		array (
			'list' => $list,
			'date' => $parameters['date'],
			'rss_title' => appconf ('rss_title'),
			'rss_description' => appconf ('rss_description'),
			'rss_date' => date ('Y-m-d\TH:i:s') . news_timezone (date ('Z')),
		)
	);
}

exit;

?>
