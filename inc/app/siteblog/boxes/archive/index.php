<?php

loader_import ('siteblog.Filters');

if (! $parameters['d']) {
	$parameters['d'] = date ('Ym');
}

$y = substr ($parameters['d'], 0, 4);
$m = substr ($parameters['d'], 4, 2);

page_title (siteblog_filter_archive_date ($parameters['d']));

if (! session_admin ()) {
	$res = db_fetch_array (
		'select * from siteblog_post where extract(YEAR_MONTH FROM created) = ? and status = "visible" order by created desc',
		$parameters['d']
	);
} else {
	$res = db_fetch_array (
		'select * from siteblog_post where extract(YEAR_MONTH FROM created) = ? order by created desc',
		$parameters['d']
	);
}

$toc = array ();

foreach (array_keys ($res) as $k) {
	$toc[$res[$k]->id] = $res[$k]->subject;
	$res[$k]->comments = db_shift ('select count(id) from siteblog_comment where child_of_post = ?', $res[$k]->id);
	$res[$k]->comments_on = true;
	$res[$k]->category_name = siteblog_filter_category ($res[$k]->category);
	if ($res[$k]->status == 'visible') {
		$res[$k]->sitellite_status = 'approved';
	} else {
		$res[$k]->sitellite_status = 'draft';
	}
}

$next = db_shift (
	'select extract(YEAR_MONTH FROM created) as d from siteblog_post where extract(YEAR_MONTH FROM created) > ? group by d order by d asc limit 1',
	$parameters['d']
);

$prev = db_shift (
	'select extract(YEAR_MONTH FROM created) as d from siteblog_post where extract(YEAR_MONTH FROM created) < ? group by d order by d desc limit 1',
	$parameters['d']
);

page_add_style (site_prefix () . '/inc/app/siteblog/html/post.css');

echo template_simple ('archive.spt', array ('post' => $res, 'toc' => $toc, 'next' => $next, 'prev' => $prev));

?>