<?php

// your app begins here

global $cgi;

if (! empty ($cgi->username) && session_admin ()) {
	header ('Location: ' . site_prefix () . '/index/cms-app?forward=' . urlencode ($_SERVER['HTTP_REFERER']));
	exit;
} elseif (! session_valid ()) {
	$action = 'login';
} elseif (! empty ($cgi->username) && $cgi->remember_me == 'yes') {
	$duration = appconf ('remember_login');
	if ($duration) {
		// convert duration to seconds
		$duration = $duration * 86400;

		// set "sitemember_remember" cookie
		global $cookie;
		$cookie->set ('sitemember_remember', $duration, $duration, '/', site_domain (), site_secure ());

		// adjust cookie
		session_change_timeout ($duration);

		// adjust expires value
		session_user_edit (
			session_username (),
			array (
				'expires' => date ('Y-m-d H:i:s', time () + $duration),
			)
		);
	}
	$action = 'home';
} else {
	$action = 'home';
}

if (session_valid () && ! empty ($parameters['goto'])) {
	header ('Location: ' . $parameters['goto']);
	exit;
}

list ($type, $call) = explode (':', appconf ($action), 2);
$func = 'loader_' . $type;
echo $func (trim ($call), $parameters, $box['context']);

?>