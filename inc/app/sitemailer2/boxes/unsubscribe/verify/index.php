<?php

$email = $parameters['email'];
$key = $parameters['key'];

function fail () {
	header ('HTTP/1.1 404 Not Found');
 	echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $_SERVER['PHP_SELF'] . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
	exit;
}

if (empty ($email)) {
	fail ();
}

$res = db_single ('select * from sitemailer2_subscriber where email = ?', $email);
if (! $res) {
	fail ();
}

if (md5 ($res->email . $res->registered) != $key) {
	fail ();
}

// valid

db_execute ('update sitemailer2_subscriber set status = ? where email = ?', 'unsubscribed', $email);
page_title (intl_get ('Thank You!'));
echo template_simple ('responses/verified_unsubscribed.spt', $parameters);

?>