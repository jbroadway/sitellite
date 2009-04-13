<?php

loader_import ('siteblog.Filters');

if (! session_admin ()) {
	$res = db_single (
		'select * from siteblog_post where id = ? and status = "visible"',
		$parameters['id']
	);
	if (! $res) {
		page_title ('Not Found');
		return;
	}
	$and = ' and status = "visible"';
} else {
	$res = db_single (
		'select * from siteblog_post where id = ?',
		$parameters['id']
	);
	$res->admin = true;
	$and = '';
}

$res =& siteblog_translate ($res);

$res->comment = db_fetch_array ('select * from siteblog_comment where child_of_post = ? order by date asc', $res->id);
$res->comments = count ($res->comment);
$res->comments_on = true;
$res->category_name = siteblog_filter_category ($res->category);
if ($res->status == 'visible') {
	$res->sitellite_status = 'approved';
} else {
	$res->sitellite_status = 'draft';
}

$prev = db_single ('select id, subject from siteblog_post where created < ?' . $and . ' order by created desc limit 1', $res->created);
$next = db_single ('select id, subject from siteblog_post where created > ?' . $and . ' order by created asc limit 1', $res->created);
$res->previous = $prev->id;
$res->prev_title = $prev->subject;
$res->next = $next->id;
$res->next_title = $next->subject;

global $cgi;

$cgi->post = $cgi->id;

page_title ($res->subject);

page_add_style (site_prefix () . '/inc/app/siteblog/html/post.css');

$res->sharethis = appconf ('sharethis');
if (! empty ($res->sharethis)) {
	page_add_script ($res->sharethis);
}

echo template_simple ('post.spt', $res);

?>