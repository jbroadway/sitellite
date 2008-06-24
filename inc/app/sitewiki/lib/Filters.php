<?php

if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/sitewiki/lib/Ext' . $join . ini_get ('include_path'));

loader_import ('saf.Date');
loader_import ('sitewiki.Ext.Text.Wiki');

function sitewiki_filter_body ($body) {
	$wiki = new Text_Wiki ();
	$wiki->setRenderConf ('xhtml', 'wikilink', 'view_url', site_prefix () . '/index/sitewiki-app/show.');
	$wiki->setRenderConf ('xhtml', 'wikilink', 'new_url', site_prefix () . '/index/sitewiki-app/show.');
	$pages = db_shift_array ('select distinct id from sitewiki_page');
	$wiki->setRenderConf ('xhtml', 'wikilink', 'pages', $pages);
	return $wiki->transform ($body, 'Xhtml');
}

function sitewiki_filter_date ($date) {
	return Date::format ($date, appconf ('date_format'));
}

function sitewiki_filter_rss_date ($date) {
	return Date::format ($date, 'D, j M Y H:i:s T');
}

function sitewiki_filter_id ($id) {
	return trim (preg_replace ('/([A-Z])/', ' \1', ucwords ($id)), 1);
}

function sitewiki_filter_level ($level) {
	switch ($level) {
		case 3:
			return intl_get ('Owner Only');
			break;
		case 2:
			return intl_get ('Admin-Level Users');
			break;
		case 1:
			return intl_get ('Registered Users');
			break;
		case 0:
			return intl_get ('Anyone');
			break;
	}
}

function sitewiki_filter_rollback ($body) {
	return str_replace ('"', '&quot;', $body);
}

?>