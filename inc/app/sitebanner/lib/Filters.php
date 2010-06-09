<?php

function sitebanner_filter_client ($client) {
	return ucwords (
		db_shift (
			'select concat(role, " - ", lastname, " ", firstname, " (", username, ")") from sitellite_user where username = ?',
			$client
		)
	);
}

function sitebanner_filter_impressions ($num) {
	$GLOBALS['sitebanner_filter_impressions'] = $num;
	return $num;
}

function sitebanner_filter_purchased ($num) {
	if ($num == -1) {
		return 'Unlimited';
	}
	return $num - $GLOBALS['sitebanner_filter_impressions'];
}

function sitebanner_virtual_clicks (&$obj) {
	$num = db_shift ('select count(*) from sitebanner_click where campaign = ?', $obj->id);
	if ($num == 0) {
		$num = '0';
	}
	$GLOBALS['sitebanner_virtual_clicks'] = $num;
	return $num;
}

function sitebanner_virtual_clicks_percent (&$obj) {
	global $sitebanner_virtual_clicks;
	if ($sitebanner_virtual_clicks > 0) {
		return number_format (($sitebanner_virtual_clicks / $obj->impressions) * 100, 2);
	}
	return '0.00';
}

function sitebanner_virtual_stats (&$obj) {
	return template_simple (
		'<a href="{site/prefix}/index/sitebanner-stats-action?id={id}">
			<img src="{site/prefix}/inc/app/cms/pix/icons/stats.gif" border="0"
				alt="{intl Click here for stats}"
				title="{intl Click here for stats}" /></a>',
		$obj
	);
}

function sitebanner_filter_date ($date) {
	global $cgi;
	return Date::format ($date, appconf ('date_format_' . $cgi->_range));
}

?>