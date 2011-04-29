<?php

$types = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
	'prefs'			=> 'preference',
	'log'			=> 'log',
);

$names = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access level',
	'prefs'			=> 'preference',
);

$pleural = array (
	'users'			=> 'users',
	'roles'			=> 'roles',
	'teams'			=> 'teams',
	'resources'		=> 'resources',
	'statuses'		=> 'statuses',
	'accesslevels'	=> 'access levels',
	'prefs'			=> 'preferences',
);

$objects = array (
	'roles'			=> 'roles',
	'teams'			=> 'teams',
	'resources'		=> 'resources',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
	'prefs'			=> 'preferences',
);

global $cgi, $session;

if (! in_array ($cgi->list, array_keys ($types))) {
	header ('Location: ' . site_prefix () . '/index/usradm-app');
	exit;
}

loader_import ('saf.GUI.Pager');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$limit = session_pref ('browse_limit');

echo loader_box ('cms/nav');

echo template_simple (USRADM_JS_ALERT_MESSAGE, $cgi);

if ($cgi->list == 'roles') {

	// roles
	include_once ('inc/app/usradm/boxes/browse/_roles.php');

} elseif ($cgi->list == 'users') {

	// users
	include_once ('inc/app/usradm/boxes/browse/_users.php');

} elseif ($cgi->list == 'teams') {

	// teams
	include_once ('inc/app/usradm/boxes/browse/_teams.php');

} elseif ($cgi->list == 'prefs') {

	// preferences
	include_once ('inc/app/usradm/boxes/browse/_prefs.php');

} elseif ($cgi->list == 'log') {

	// activity log
	include_once ('inc/app/usradm/boxes/browse/_log.php');

} elseif ($cgi->list == 'resources') {

	// resources
	include_once ('inc/app/usradm/boxes/browse/_resources.php');

} else {

	// easy ones (statuses, accesslevels, resources)
	include_once ('inc/app/usradm/boxes/browse/_simple.php');

}

//exit;

?>