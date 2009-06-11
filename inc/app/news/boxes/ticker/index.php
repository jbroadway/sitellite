<?php

if ($box['context'] == 'action') {
	global $cgi;
	
	loader_import ('news.Story');
	loader_import ('news.Functions');
	
	$story = new NewsStory;
	
	$params = array ();
	
	if (! empty ($parameters['section'])) {
		$params['category'] = $parameters['section'];
	}
	
	$story->limit (5);
	$story->orderBy ('date desc, rank desc, id desc');
	
	$list = $story->find ($params);
	if (! $list) {
		$list = array ();
	}

	page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
	page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');

	echo template_simple ('ticker.spt', array (
		'list' => $list,
		'bg' => $parameters['bg'],
		'width' => $parameters['width'],
		'border' => $parameters['border'],
	));
	exit;
} else {
	echo template_simple ('ticker_iframe.spt', $parameters);
}

?>