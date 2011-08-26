<?php

if (! session_valid()) {
    header('Location: /index/sitemember-app');
    exit;
}

loader_import('digger.Filters');

$user = session_username();

$stories = db_fetch_array('select * from digger_linkstory where user = ? order by posted_on desc limit 10', $user);
$votes = db_fetch_array('select l.*, v.score as my_vote from digger_linkstory l, digger_vote v where v.story = l.id and v.user = ? order by v.votetime desc limit 10', $user);
$comments = db_fetch_array('select l.*, c.id as comment_id from digger_linkstory l, digger_comments c where c.story = l.id and c.user = ? order by c.comment_date desc limit 10', $user);

echo template_simple('my_home.spt',
	array(
		'stories' => $stories,
		'votes' => $votes,
		'comments' => $comments,
	)
);

?>