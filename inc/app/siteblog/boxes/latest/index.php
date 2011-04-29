<ul>
<?php

if (! $parameters['limit']) {
	$parameters['limit'] = 5;
}

loader_import ('siteblog.Filters');

$res = db_fetch_array (
	'select id, subject from siteblog_post where status = "visible" order by created desc limit ' . $parameters['limit']
);

foreach ($res as $row) {
	echo '<li><a href="'
		. site_prefix ()
		. '/index/siteblog-post-action/id.' . $row->id . '/title.' . siteblog_filter_link_title ($row->subject) . '">'
		. $row->subject . '</a></li>';
}

?>
</ul>