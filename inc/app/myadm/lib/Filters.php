<?php

loader_import ('saf.Date');

function myadm_report_run ($obj) {
	return '<a href="' . site_prefix () . '/index/myadm-report-action?id='
		. $obj->id . '"><img src="' . site_prefix ()
		. '/inc/app/cms/pix/icons/stats.gif" border="0" alt="'
		. intl_get ('View Report') . '" title="'
		. intl_get ('View Report') . '" /></a>';
}

function myadm_filter_datetime ($date) {
	return Date::timestamp ($date, appconf ('format_date_time'));
}

?>