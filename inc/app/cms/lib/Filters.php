<?php

loader_import ('saf.Date');

/**
 * @package CMS
 */
function cms_filter_date ($date) {
    return intl_date ($date);
}

/**
 * @package CMS
 */
function cms_filter_time ($date) {
	return intl_time ($date);
}

/**
 * @package CMS
 */
function cms_filter_ts_time ($date) {
	return intl_time ($date);
}

/**
 * @package CMS
 */
function cms_filter_date_time ($date) {
	return intl_datetime ($date);
}

function cms_virtual_filesystem_download (&$obj) {
	return db_shift (
		'select count(*) from sitellite_filesystem_download where name = ?',
		$obj->name
	);
}

?>