<?php

loader_import ('saf.Date');

function siteblog_filter_datetime ($date) {
    return Date::format ($date, 'F j, Y - g:i A');
}

function siteblog_filter_nicedate ($date) {
    return Date::format ($date, 'F jS, Y');
}

function siteblog_filter_link_title ($t) {
	return trim (strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$t
		)
	), '-');
}

function siteblog_filter_comment ($c) {
	return preg_replace (
		'/(\r\n|\r|\n)/s',
		'<br />$1',
		htmlentities_compat ($c)
	);
}

function siteblog_filter_category ($id) {
	return db_shift ('select title from siteblog_category where id = ?', $id);
}

function siteblog_filter_status ($s) {
	if ($s == 'visible') {
		return 'Published';
	}
	return 'Draft';
}

function siteblog_filter_blog_link ($id) {
	$title = db_shift (
		'select subject from siteblog_post where id = ?',
		$id
	);
	return '<a href="' . site_prefix () . '/index/siteblog-post-action/id.' . $id . '/title.' . siteblog_filter_link_title ($title) . '">' . $title . '</a>';
}

function siteblog_filter_ip ($ip) {
	return $ip . ' <a href="' . site_prefix () . '/index/siteblog-ban-action?ip=' . $ip . '" title="' . intl_get("Click to ban this IP address from commenting.") . '" onclick="return confirm (\'' . intl_get ("Are you sure you want to ban this IP address?") . '\')">BAN</a>';
}

function siteblog_filter_archive_date ($d) {
	$months = array (
		'01' => intl_get ('January'),
		'02' => intl_get ('February'),
		'03' => intl_get ('March'),
		'04' => intl_get ('April'),
		'05' => intl_get ('May'),
		'06' => intl_get ('June'),
		'07' => intl_get ('July'),
		'08' => intl_get ('August'),
		'09' => intl_get ('September'),
		'10' => intl_get ('October'),
		'11' => intl_get ('November'),
		'12' => intl_get ('December'),
	);
	return $months[substr ($d, 4, 2)] . ' ' . substr ($d, 0, 4);
}

function siteblog_filter_link ($url) {
	return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
}

function siteblog_filter_akismet ($cid) {
	return '<br />[ <a href="' . site_prefix () . '/index/siteblog-akismet-spam-action?id=' . $cid . '">Spam?</a> ]';
}

function siteblog_filter_body ($b) {
	if (! appconf ('split_body')) {
		return template_parse_body ($b);
	}

	return template_parse_body (
		preg_replace ('|<hr[^>]*>|is', '', $b)
	);
}

function siteblog_filter_body_summary ($b) {
	if (! appconf ('split_body')) {
		return template_parse_body ($b);
	}

	return template_parse_body (
		array_shift (
			preg_split ('|<hr[^>]*>|is', $b)
		)
	);
}

function &siteblog_translate (&$obj) {
	loader_import ('saf.Database.Generic');
	$g = new Generic ('siteblog_post', 'id');
	$g->multilingual = true;
	$res =& $g->translate ($obj);
	return $res;
}

?>