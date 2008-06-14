<?php

loader_import ('news.Story');
loader_import ('news.Functions');

$s = new NewsStory ();
$story = $s->get ($parameters['story']);
$story->show_thumb = $parameters['thumb'];
$story->show_date = $parameters['date'];
$story->show_summary = $parameters['summary'];

echo template_simple ('sidebar_single.spt', $story);

?>