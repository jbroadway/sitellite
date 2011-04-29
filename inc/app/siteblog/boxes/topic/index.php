<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #177 Pagination.
//

loader_import ('siteblog.Filters');

page_title (intl_get ('Topic') . ': ' . siteblog_filter_category ($parameters['id']));

$res = db_fetch_array (
	'select * from siteblog_post where category = ? and status = "visible" order by created desc',
	$parameters['id']
);

global $cgi;

if (! $cgi->offset) {
	$cgi->offset = 0;
}

loader_import ('saf.GUI.Pager');

if (! session_admin ()) {
	$q = db_query (
		'select * from siteblog_post where category = ? and status = "visible" order by created desc'
	);
	if (! $q->execute ($parameters['id'])) {
		die ($q->error);
	}
	$total = $q->rows ();
	$res = $q->fetch ($cgi->offset, appconf ('limit'));
	$q->free ();
} else {
	$q = db_query (
		'select * from siteblog_post where category = ? order by created desc'
	);
	if (! $q->execute ($parameters['id'])) {
		die ($q->error);
	}
	$total = $q->rows ();
	$res = $q->fetch ($cgi->offset, appconf ('limit'));
	$q->free ();
}

$res =& siteblog_translate ($res);

// Start: SEMIAS #177 Pagination.
// 	Not sure a fix is needed here.
$pg = new Pager ($cgi->offset, appconf ('limit'), $total);
$pg->setUrl (site_prefix () . '/index/siteblog-topic-action/id.' . $parameters['id'] . '/title.' . $parameters['title']);
$pg->getInfo ();
// END: SEMIAS

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

$sharethis = appconf ('sharethis');
if (! empty ($sharethis)) {
	page_add_script ($sharethis);
}

echo template_simple ('posts.spt', array ('post' => $res, 'sharethis' => $sharethis));

?>