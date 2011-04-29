<?php

if ($context == 'action') {
	page_title (appconf ('title'));
}

$listings = db_fetch_array (
	'select * from realty_listing where status != "archived" order by ts desc'
);

echo template_simple ('listings.spt', $listings);

?>