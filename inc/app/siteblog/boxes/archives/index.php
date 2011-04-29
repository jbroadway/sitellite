<ul>
<?php

loader_import ('siteblog.Filters');

$res = db_shift_array (
	'select extract(YEAR_MONTH FROM created) as d from siteblog_post group by d order by d desc limit 10'
);

$months = array (
	'01' => intl_get ('January'),
	'02' => intl_get ('February'),
	'03' => intl_get ('March'),
	'04' => intl_get ('April'),
	'05' => intl_get ('May'),
	'06' => intl_get ('June'),
	'07' => intl_get ('July'),
	'08' => intl_get ('August'),
	'09' => intl_get ('September'),
	'10' => intl_get ('October'),
	'11' => intl_get ('November'),
	'12' => intl_get ('December'),
);

foreach ($res as $d) {
	echo '<li><a href="'
		. site_prefix ()
		. '/index/siteblog-archive-action/d.' . $d . '">'
		. siteblog_filter_archive_date ($d) . '</a></li>';
}

?>
</ul>