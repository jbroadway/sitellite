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

$on = appconf ('changepass');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'box:sitemember/changepass') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

if (! session_valid ()) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
}

global $cgi, $session;

if (better_crypt_compare ($cgi->current, session_password ()) && ! empty ($cgi->newpass) && $cgi->verify == $cgi->newpass) {
	$session->update (
		array (
			'password' => better_crypt ($cgi->newpass),
			'expires' => date ('Y-m-d H:i:s', time () + 3600),
		),
		$session->username
	);
	page_title (intl_get ('Password Changed'));
	echo template_simple ('pass_changed.spt');
} else {
	$data = array ();
	if (! empty ($cgi->newpass)) {
		$data['error'] = true;
	}
	page_title (intl_get ('Change Password'));
	echo template_simple ('changepass.spt', $data);
}

?>