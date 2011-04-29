<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

appconf_set ('valid', array ('mp3', 'ogg', 'wma', 'ra', 'rm', 'wav', 'aif', 'aiff', 'qt', 'mov', 'mpg', 'mpeg', 'mp2', 'mp4', 'wmv', 'avi', 'rv'));

appconf_set ('mimes', array (
	'mp3' => 'audio/mpeg',
	'ogg' => 'application/ogg',
	'wma' => 'audio/x-ms-wma',
	'ra' => 'audio/x-pn-realaudio',
	'rm' => 'audio/x-pn-realaudio',
	'ram' => 'audio/x-pn-realaudio',
	'wav' => 'audio/x-wav',
	'aif' => 'audio/x-aiff',
	'aiff' => 'audio/x-aiff',
	'qt' => 'video/quicktime',
	'mov' => 'video/quicktime',
	'mpg' => 'video/mpeg',
	'mpeg' => 'video/mpeg',
	'mp2' => 'video/mpeg',
	'mp4' => 'video/mp4',
	'wmv' => 'video/x-ms-wmv',
	'avi' => 'video/x-msvideo',
	'rv' => 'video/vnd.rn-realvideo',
));

?>