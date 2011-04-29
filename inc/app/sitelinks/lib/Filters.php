<?php

function sitelinks_filter_ts ($ts) {
	loader_import ('saf.Date');
	return Date::timestamp ($ts, appconf ('date_time'));
}

function sitelinks_filter_get_title ($id) {
	return db_shift ('select title from sitelinks_item where id = ?', $id);
}

function sitelinks_filter_shortdate ($date) {
	loader_import ('saf.Date');

	global $cgi;

	switch ($cgi->top_range) {
		case 'day':
			$format = appconf ('admin_date_short');
			break;
		case 'month':
			$format = appconf ('admin_date_month');
			break;
		case 'year':
			$format = appconf ('admin_date_year');
			break;
	}

	return Date::format ($date, $format);
}

function sitelinks_filter_status ($status) {
	if ($status == 'approved') {
		return intl_get ('Live');
	} elseif ($status == 'rejected') {
		return intl_get ('Rejected');
	}
	return intl_get ('Pending');
}

?>