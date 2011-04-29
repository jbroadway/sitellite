<?php

$types = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
	'prefs'			=> 'pref'
);

$names = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access level',
	'prefs'			=> 'preference'
);

global $cgi;

//if ($cgi->_list == 'users' && $cgi->_key == 'master') {
//    header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list . '&_msg=' . urlencode ('Cannot delete master user.'));
//	exit;
//}

if (! in_array ($cgi->_list, array_keys ($types))) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action');
	exit;
}

if ($cgi->_list == 'roles' && $cgi->_key == session_role ()) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list . '&_msg=' . urlencode ('Cannot delete the specified role, because it is the role of the current user.'));
	exit;
}

if ($cgi->_list == 'teams' && $cgi->_key == session_team ()) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list . '&_msg=' . urlencode ('Cannot delete the specified team, because it is the team of the current user.'));
	exit;
}

$snm =& session_get_manager ();
$snm->{$types[$cgi->_list]}->delete ($cgi->_key);

header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list);
exit;

?>