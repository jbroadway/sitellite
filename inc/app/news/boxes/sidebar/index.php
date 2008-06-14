<?php

loader_import ('news.Story');
loader_import ('news.Functions');

$story = new NewsStory;

$story->limit ($parameters['limit']);
$story->orderBy ('date desc, rank desc, id desc');

if (! isset ($parameters['sec'])) {
	$params = array ();
} else {
	$params = array ('category' => $parameters['sec']);
}

$res = $story->find ($params);
if (! $res) {
	$res = array ();
}

echo template_simple ('sidebar.spt', array ('list' => $res, 'dates' => $parameters['dates'], 'thumbs' => $parameters['thumbs']));

?>