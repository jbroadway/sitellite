<?php

$ak = appconf ('akismet_key');
if (! $ak) {
	header ('Location: ' . site_prefix () . '/index/siteblog-akismet-action');
	exit;
}

loader_import ('siteblog.Filters');
loader_import ('siteblog.Akismet');

$comment = (array) db_single (
	'select * from siteblog_akismet where id = ?',
	$parameters['id']
);

unset ($comment['id']);

$title = db_shift ('select subject from siteblog_post where id = ?', $comment['post_id']);

$comment['permalink'] = site_url () .'/index/siteblog-post-action/id.' . $comment['post_id'] . '/title.' . siteblog_filter_link_title ($title);

$pid = $comment['post_id'];

unset ($comment['post_id']);

$akismet = new Akismet (site_url (), $ak, $comment);

if (! $akismet->errorsExist ()) {
	// no errors
	switch ($parameters['spam']) {
		case 'yes':
			$akismet->submitSpam ();
			db_execute ('delete from siteblog_akismet where id = ?', $parameters['id']);
			break;
		case 'no':
			$akismet->submitHam ();
			db_execute (
				'insert into siteblog_comment values (null, ?, ?, ?, ?, ?, ?, 0, ?)',
				$comment['ts'],
				$comment['author'],
				$comment['email'],
				$comment['website'],
				$comment['user_ip'],
				$pid,
				$comment['body']
			);
			db_execute ('delete from siteblog_akismet where id = ?', $parameters['id']);
			break;
	}
}

header ('Location: ' . site_prefix () . '/index/siteblog-akismet-action');
exit;

?>