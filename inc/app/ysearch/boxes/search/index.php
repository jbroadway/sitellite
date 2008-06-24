<?php

global $cgi;

$parameters = get_object_vars ($cgi);

if (! $cgi->offset) {
	$cgi->offset = 0;
}

loader_import ('ysearch.Ysearch');
loader_import ('ysearch.Filters');
loader_import ('saf.Misc.Search');

if (! empty ($parameters['site'])) {
	$site = $parameters['site'];
} else {
	$site = appconf ('site');
}

$y = new Ysearch (appconf ('appid'), $site);

if ($box['context'] == 'action') {
	page_title (intl_get ('Search'));
}

if (! empty ($parameters['query'])) {
	loader_import ('saf.GUI.Pager');

	$res = $y->query ($parameters['query'], $cgi->offset);
	if (! $res) {
		page_title ('Search Error');
		echo '<p>' . $y->error . '</p>';
		return;
	} elseif (isset ($res['Error'])) {
		page_title ('Search Error');
		echo '<p>' . $res['Error']['Message'] . '</p>';
		return;
	}

	$parameters['results'] = array ();
	foreach ($res['ResultSet']['Result'] as $result) {
		$result['CacheUrl'] = $result['Cache']['Url'];
		$result['CacheSize'] = ysearch_filter_size ($result['Cache']['Size']);
		$result['Title'] = ysearch_filter_title ($result['Title']);
		$result['Title'] = search_highlight ($result['Title'], $cgi->query);
		$result['Summary'] = search_highlight ($result['Summary'], $cgi->query);
		$parameters['results'][] = $result;
	}

	$pg = new Pager ($cgi->offset, 25, $res['ResultSet']['totalResultsAvailable']);
	$pg->setUrl (site_current () . '?query=%s&site=%s', $parameters['query'], $parameters['site']);
	$pg->getInfo ();

	template_simple_register ('pager', $pg);

	echo template_simple ('search_results.spt', $parameters);
} else {
	echo template_simple ('search_form.spt');
}

?>