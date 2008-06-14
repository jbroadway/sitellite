<?php

loader_import ('help.Help');

if (empty ($parameters['appname'])) {
	$parameters['appname'] = 'cms';
}

if (empty ($parameters['lang'])) {
	$parameters['lang'] = 'en';
}

if (! empty ($parameters['query'])) {
	$results = help_search ($parameters['appname'], $parameters['query'], $parameters['lang']);

	if (! $results) {
		$results = array ();
	} elseif (is_object ($results)) {
		$results = array ($results);
	}
} else {
	$results = array ();
}

page_id ('search');
page_title (intl_get ('Help Search'));
echo template_simple ('search.spt', array ('query' => $parameters['query'], 'results' => $results));

?>