<?php

$types = array (
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
);

global $cgi;

if (! in_array ($cgi->_list, array_keys ($types))) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action');
	exit;
}

$snm =& session_get_manager ();

$snm->{$types[$cgi->_list]}->add ($cgi->_key);

header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list);

exit;

?>