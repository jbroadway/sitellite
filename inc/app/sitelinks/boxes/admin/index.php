<?php

// your admin UI begins here

page_title (intl_get ('SiteLinks'));

loader_import ('sitelinks.Functions');
loader_import ('sitelinks.Filters');

global $cgi;

$data = new StdClass;

if (empty ($cgi->top_range)) {
	$cgi->top_range = 'month';
}
$data->top_range = $cgi->top_range;

if (empty ($cgi->top_date)) {
	$cgi->top_date = date ('Y-m-d');
}
$data->top_date = $cgi->top_date;

list ($start, $end) = sitelinks_get_range ($cgi->top_range, $cgi->top_date);
$data->top_start = $start;
$data->top_end = $end;
list ($prev, $next) = sitelinks_get_dates ($cgi->top_range, $cgi->top_date);
$data->top_prev = $prev;
$data->top_next = $next;

// views
$list = db_fetch_array (
	'select item_id, count(*) as total from sitelinks_view where ts >= ? and ts <= ? group by item_id order by total desc limit 10',
	$start,
	$end
);

$data->views = $list;

// hits
$list = db_fetch_array (
	'select item_id, count(*) as total from sitelinks_hit where ts >= ? and ts <= ? group by item_id order by total desc limit 10',
	$start,
	$end
);

$data->hits = $list;

// ratings
$list = db_fetch_array (
	'select item_id, avg(rating) as rating, count(*) as votes from sitelinks_rating where ts >= ? and ts <= ? group by item_id order by rating desc limit 10',
	$start,
	$end
);

foreach (array_keys ($list) as $k) {
	$list[$k]->rating = number_format ($list[$k]->rating, 2);
}

$data->ratings = $list;

$data->drafts = db_shift ('select count(*) from sitelinks_item where sitellite_status = "draft"');

echo template_simple ('admin.spt', $data);

?>