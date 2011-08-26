<?php

// BEGIN KEEPOUT CHECKING
if (! defined ('SAF_VERSION')) {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END KEEPOUT CHECKING

// show an rss newsfeed of files from the specified path

if (! isset ($parameters['path'])) {
	$parameters['path'] = '';
}

if (session_admin ()) {
	$acl = session_allowed_sql ();
} else {
	$acl = session_approved_sql ();
}

$res = db_fetch_array (
	'select name, extension
	from sitellite_filesystem
	where
		path = ? and
		' . $acl,
	$parameters['path']
);

if (count ($res) == 0) {
	//return;
}

if (! empty ($parameters['path'])) {
	$parameters['path'] .= '/';
}

loader_import ('news.Functions');

header ('Content-Type: text/xml');
echo template_simple (
	'filesystem_rss.spt',
	array (
		'path' => $parameters['path'],
		'rss_title' => ($parameters['path']) ? $parameters['path'] : 'default',
		'list' => $res,
		'rss_date' => date ('Y-m-d\TH:i:s') . news_timezone (date ('Z')),
	)
);
exit;

?>