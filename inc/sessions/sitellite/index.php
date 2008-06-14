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

// inc/sessions/sitellite/index.php
// the default sitellite session definition file
// note: session handlers aren't eval()'d, they are include()'d
// so there is no need to import global objects and any objects
// created are also already global, since there is no namespace
// separation.  this is how session definitions differ from
// boxes.

// include our session settings file
// note: session definitions can use the dirname (__FILE__) syntax
// but boxes cannot, because they are called in a separate manner.
// you must be aware of this should you try cutting and pasting
// code between session definitions and boxes.
$_sconf = parse_ini_file (dirname (__FILE__) . '/settings.php', true);

// evaluation condition.  this determines whether or not this
// session should be evaluated.
//if (
//	(! empty ($cgi->username) && ! empty ($cgi->password)) ||
//	! empty ($cookie->{$_sconf['Handler']['cookiename']})
//	) {

$sources = array ();
foreach ($_sconf as $k => $v) {
	if (strpos ($k, 'Source ') === 0) {
		$sources[$v['driver']] = $v;
	}
}

list ($user, $pass, $id) = @Session::gatherParameters ($_sconf['Handler']['driver'], $_sconf['Handler']['cookiename']);
$session = new Session ($_sconf['Handler']['driver'], array_keys ($sources), $_sconf['Store']['driver'], $user, $pass, $id);
$session->init ($_sconf['Session']['path']);

$session->setTimeout ($_sconf['Session']['timeout']);
foreach ($sources as $k => $v) {
	$session->setSourceProperties ($k, $v);
}
$session->setHandlerProperties ($_sconf['Handler']);
$session->setStoreProperties ($_sconf['Store']);

if ($_sconf['Handler']['driver'] == 'Cookie' && $conf['Site']['secure']) {
	$session->handler->cookiesecure = true;
	$session->store->cookiesecure = 1;
}

if ((! empty ($cgi->username)) || (! empty ($cookie->{$_sconf['Handler']['cookiename']}))) {
	if ($cookie->sitemember_remember > 0) {
		$session->handler->cookieexpires = $cookie->sitemember_remember;
		$session->setTimeout ($cookie->sitemember_remember);
		$cookie->set ('sitemember_remember', $cookie->sitemember_remember, $cookie->sitemember_remember, '/', site_domain (), site_secure ());
	}
	$session->start ();
}

//} // end evaluation condition

?>