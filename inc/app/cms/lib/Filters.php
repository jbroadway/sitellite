<?php

loader_import ('saf.Date');

/**
 * @package CMS
 */
function cms_filter_date ($date) {
	return Date::format ($date, appconf ('format_date'));
}

/**
 * @package CMS
 */
function cms_filter_time ($date) {
	return Date::time ($date, appconf ('format_time'));
}

/**
 * @package CMS
 */
function cms_filter_ts_time ($date) {
	return Date::timestamp ($date, appconf ('format_time'));
}

/**
 * @package CMS
 */
function cms_filter_date_time ($date) {
	return Date::timestamp ($date, appconf ('format_date_time'));
}

function cms_virtual_filesystem_download (&$obj) {
	return db_shift (
		'select count(*) from sitellite_filesystem_download where name = ?',
		$obj->name
	);
}

?>