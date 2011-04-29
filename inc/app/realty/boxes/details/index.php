<?php

$listing = db_single (
	'select * from realty_listing where id = ?',
	$parameters['id']
);

if (! is_object ($listing)) {
	header ('Location: ' . site_prefix () . '/index/realty-app');
	exit;
	//page_title ('Error');
	//echo 'Listing not found';
	//return;
}

page_title ($listing->headline . ' - Asking ' . realty_filter_price ($listing->price));
echo template_simple ('listing.spt', $listing);

?>