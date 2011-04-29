<?php

if (@file_exists ('inc/app/sitesearch')) {
	header ('Location: ' . site_prefix () . '/index/sitesearch-app');
	exit;
}
if (@file_exists ('inc/app/ysearch')) {
	header ('Location: ' . site_prefix () . '/index/ysearch-app');
	exit;
}

if (! empty ($parameters['query'])) {
	$results = db_fetch (
		'select id, title, description, concat(substring(body, 1, 125), "...") as summary from sitellite_page where title like ? or body like ?',
		'%' . $parameters['query'] . '%',
		'%' . $parameters['query'] . '%'
	);

	if (! $results) {
		$results = array ();
	} elseif (is_object ($results)) {
		$results = array ($results);
	}
} else {
	$results = array ();
}

if ($box['context'] == 'action') {
	page_id ('search');
	page_title ('Search');
}
echo template_simple ('search.spt', array ('query' => $parameters['query'], 'results' => $results));

?>
