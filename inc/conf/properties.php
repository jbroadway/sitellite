<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

// custom application properties go here.
// this helps centralize values that would be often referenced
// in multiple places in your application.

define ('E_UNAUTHORIZED',		401);
define ('E_FORBIDDEN',			403);
define ('E_NOT_FOUND',			404);
define ('E_SERVER_ERROR',		500);

conf_set ('errors',
	array (
		E_UNAUTHORIZED		=> array (
			'title'			=> 'Unauthorized',
			'message'		=> 'You do not have the permission to view the requested document.',
		),
		E_FORBIDDEN			=> array (
			'title'			=> 'Forbidden',
			'message'		=> 'The requested document is forbidden.',
		),
		E_NOT_FOUND			=> array (
			'title'			=> 'Not Found',
			'message'		=> 'The requested document was not found on this server.',
		),
		E_SERVER_ERROR		=> array (
			'title'			=> 'Server Error',
			'message'		=> 'The server has encountered an unknown internal error.',
		),
	)
);

formdata_set (
	'provinces',
	array (
		'Alberta',
		'British Columbia',
		'Manitoba',
		'New Brunswick',
		'Newfoundland',
		'Northwest Territories',
		'Nova Scotia',
		'Nunavut',
		'Ontario',
		'Quebec',
		'Prince Edward Island',
		'Saskatchewan',
		'Yukon Territories',
	)
);

formrules_set (
	'username',
	array (
		array ('not empty', 'You must enter a username to continue.'),
		array ('unique "sitellite_user/username"', 'The username you have chosen is already in use.'),
	)
);

if (session_admin ()) {
	page_add_style (site_prefix () . '/inc/html/admin/extra.css');
}

?>