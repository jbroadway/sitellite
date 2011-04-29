<?php

$ak = appconf ('akismet_key');
if (! $ak) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

loader_import ('siteblog.Filters');

$c = db_single ('select * from siteblog_comment where id = ?', $parameters['id']);

db_execute ('delete from siteblog_comment where id = ?', $parameters['id']);

$title = db_shift ('select subject from siteblog_post where id = ?', $vals['post']);

$comment = array (
	'author' => $c->name,
	'email' => $c->email,
	'website' => $c->url,
	'body' => $c->body,
	'permalink' => site_url () . '/index/siteblog-post-action/id.' . $c->post_id . '/title.' . siteblog_filter_link_title ($title),
	'user_ip' => $c->ip,
	'user_agent' => '',
);
loader_import ('siteblog.Akismet');

$akismet = new Akismet (site_url (), $ak, $comment);

if (! $akismet->errorsExist ()) {
	// no errors
	$akismet->submitSpam ();
}

header ('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>