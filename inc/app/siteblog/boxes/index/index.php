<?php

loader_import ('siteblog.Filters');

if ($context == 'action') {
	page_title (appconf ('blog_name'));
}

global $cgi;

if (! $cgi->offset) {
	$cgi->offset = 0;
}

loader_import ('saf.GUI.Pager');

if (! session_admin ()) {
	$q = db_query (
		'select * from siteblog_post where status = "visible" order by created desc'
	);
	if (! $q->execute ()) {
		die ($q->error);
	}
	$total = $q->rows ();
	$res = $q->fetch ($cgi->offset, appconf ('limit'));
	$q->free ();
} else {
	$q = db_query (
		'select * from siteblog_post order by created desc'
	);
	if (! $q->execute ()) {
		die ($q->error);
	}
	$total = $q->rows ();
	$res = $q->fetch ($cgi->offset, appconf ('limit'));
	$q->free ();
}

$res =& siteblog_translate ($res);

$pg = new Pager ($cgi->offset, appconf ('limit'), $total);
$pg->setUrl (site_prefix () . '/index/siteblog-app');
$pg->getInfo ();

template_simple_register ('pager', $pg);

foreach (array_keys ($res) as $k) {
	$res[$k]->comments = db_shift ('select count(id) from siteblog_comment where child_of_post = ?', $res[$k]->id);
	$res[$k]->comments_on = true;
	$res[$k]->category_name = siteblog_filter_category ($res[$k]->category);
	if ($res[$k]->status == 'visible') {
		$res[$k]->sitellite_status = 'approved';
	} else {
		$res[$k]->sitellite_status = 'draft';
	}
}

page_add_style (site_prefix () . '/inc/app/siteblog/html/post.css');

echo template_simple ('posts.spt', array ('post' => $res));

?>