<?php

if (! session_valid ()) {
	header ('Location: ' . site_prefix () . '/');
	exit;
}

$banners = db_fetch_array (
	'select
		id, name, url, file, impressions, purchased, active
	from
		sitebanner_ad
	where
		client = ?
	order by
		active asc, name asc',
	session_username ()
);

loader_import ('sitebanner.Filters');

foreach (array_keys ($banners) as $k) {
	$banners[$k]->impressions = sitebanner_filter_impressions ($banners[$k]->impressions);
	$banners[$k]->purchased = sitebanner_filter_purchased ($banners[$k]->purchased);
	$banners[$k]->clicks = sitebanner_virtual_clicks ($banners[$k]);
	$banners[$k]->clicks_percent = sitebanner_virtual_clicks_percent ($banners[$k]);
}

echo template_simple (
	'client.spt',
	array (
		'list' => $banners,
	)
);

?>