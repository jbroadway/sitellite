<?php

loader_import('digger.Filters');
loader_import('digger.Functions');

$story = db_fetch_array(
	'SELECT * FROM digger_linkstory
	WHERE id=?',
	$parameters['id']
);

$comments = db_fetch_array(
	'SELECT * FROM digger_comments
	WHERE story=?',
	$parameters['id']
);

if (digger_has_voted($story[0]->id)) {
    $story[0]->voted = 'style="display: none"';
}

page_title($story[0]->title);

echo template_simple('comments.spt',
	array(
		'story' => $story,
		'comments' => $comments,
		'banned_score' => appconf('ban_threshold'),
	)
);

?>