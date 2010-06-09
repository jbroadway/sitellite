<?php

/**
 * @package siteconnector
 */
function siteconnector_filter_mtime ($mtime) {
	return date (appconf ('date_format'), $mtime);
}

/**
 * @package siteconnector
 */
function siteconnector_filter_datetime ($mtime) {
	return Date::timestamp ($mtime, appconf ('date_format'));
}

/**
 * @package siteconnector
 */
function siteconnector_filter_shortdate ($date) {
	loader_import ('saf.Date');

	global $cgi;

	switch ($cgi->top_range) {
		case 'day':
			$format = appconf ('date_short');
			break;
		case 'month':
			$format = appconf ('date_month');
			break;
		case 'year':
			$format = appconf ('date_year');
			break;
	}

	return Date::format ($date, $format);
}

/**
 * @package siteconnector
 */
function siteconnector_filter_month ($date) {
	loader_import ('saf.Date');
	return Date::format ($date, appconf ('date_month'));
}

/**
 * @package siteconnector
 */
function siteconnector_filter_results ($results) {
	return ceil ($results);
}

?>