<ul>
<?php

loader_import ('siteblog.Filters');

foreach (db_pairs ('select id, title from siteblog_category order by title asc') as $id => $title) {
	$count = db_shift ('select count(*) from siteblog_post where category = ?', $id);
	echo '<li><a href="'
		. site_prefix ()
		. '/index/siteblog-topic-action/id.' . $id . '/title.' . siteblog_filter_link_title ($title) . '">'
		. $title . ' (' . $count . ')</a></li>';
}

?>
</ul>