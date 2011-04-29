<?php

function sitesearch_filter_mtime ($mtime) {
	return date (appconf ('date_format'), $mtime);
}

function sitesearch_filter_datetime ($mtime) {
	return Date::timestamp ($mtime, appconf ('date_format'));
}

function sitesearch_filter_time ($mtime) {
	return Date::timestamp ($mtime, appconf ('time_format'));
}

function sitesearch_filter_shortdate ($date) {
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
		default:
			$format = appconf ('date_short');
	}

	return Date::format ($date, $format);
}

function sitesearch_filter_month ($date) {
	loader_import ('saf.Date');
	return Date::format ($date, appconf ('date_month'));
}

function sitesearch_filter_duration ($duration) {
	$min = floor ($duration / 60);
	$sec = ceil ($duration % 60);
	return $min . ' ' . intl_get ('minutes') . ', ' . $sec . ' ' . intl_get ('seconds');
}

function sitesearch_filter_uptime ($duration) {
	$days = floor ($duration / 86400);
	$hours = floor (($duration - ($days * 86400)) / 3600);
	$mins = ceil (($duration - ($days * 86400) - ($hours * 3600)) / 60);
	return $days . ' ' . intl_get ('days') . ', ' . $hours . ' ' . intl_get ('hours') . ', ' . $mins . ' ' . intl_get ('minutes');
}

function sitesearch_filter_ctype ($ctype) {
	$info = ini_parse ('inc/app/cms/conf/collections/' . $ctype . '.php');
	if (isset ($info['Collection']['display'])) {
		return ($info['Collection']['display']);
	}
	return $ctype;
}

function sitesearch_filter_results ($results) {
	return ceil ($results);
}

function sitesearch_filter_highlight ($text) {

	$text = strip_tags (xmlentities_reverse ($text));

	if (strlen ($text) >= 300) {
		$text = substr ($text, 0, 297);
	}

	loader_import ('saf.Misc.Search');
	global $cgi, $sitesearch_queries;
	if (! is_array ($sitesearch_queries)) {
		$sitesearch_queries = search_split_query ($cgi->query);
	}
	foreach ($sitesearch_queries as $query) {
		$text = preg_replace ('/(' . preg_quote ($query, '/') . ')/i', '<strong>\1</strong>', $text);
	}
	return $text;
}

function sitesearch_filter_score ($score) {
	return round ($score * 100, 0);
}

function sitesearch_timezone ($offset) {
	$out = $offset[0];
	$offset = substr ($offset, 1);
	$h = floor ($offset / 3600);
	$m = floor (($offset % 3600) / 60);
	return $out . str_pad ($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad ($m, 2, '0', STR_PAD_LEFT);
}

function sitesearch_filter_query ($q) {
	return str_replace ('"', '&quot;', htmlentities_compat ($q));
}

?>
