<?php

if (session_admin ()) {
	$allowed = session_allowed_sql ();
} else {
	$allowed = session_approved_sql ();
}

$pres = db_single (
	'select * from sitepresenter_presentation where id = ? and ' . $allowed,
	$parameters['id']
);

if (! $pres) {
	header ('Location: ' . site_prefix () . '/index/sitepresenter-app');
	exit;
}

loader_import ('saf.Date');

$pres->date = Date::format ($pres->ts, 'Ymd');
$pres->fmdate = Date::format ($pres->ts, 'F j, Y');

$res = db_single (
	'select concat(firstname, " ", lastname) as author, company from sitellite_user where username = ?',
	$pres->sitellite_owner
);

$pres->author = $res->author;

$pres->company = $res->company;

$doms = explode ('.', site_domain ());
$pres->domain = array_pop ($doms);
$pres->domain = '.' . $pres->domain;
$pres->domain = array_pop ($doms) . $pres->domain;

$pres->slides = db_fetch_array (
	'select * from sitepresenter_slide where presentation = ? order by number asc',
	$parameters['id']
);

db_execute (
	'insert into sitepresenter_view (presentation, ts, ip) values (?, now(), ?)',
	$parameters['id'],
	$_SERVER['REMOTE_ADDR']
);

if (isset ($parameters['theme']) && ! strpos ($parameters['theme'], '..') && @is_dir ('inc/app/sitepresenter/themes/' . $parameters['theme'])) {
	$pres->theme = $parameters['theme'];
}

echo template_simple ('presentation.spt', $pres);

exit;

?>