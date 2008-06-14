<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $_SERVER['PHP_SELF'] . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

// import any object we need from the global namespace
global $errno, $cgi;

// box logic begins here

$errors = conf ('errors');

if (! $errno) {
	$errno = $cgi->code;
}

loader_import ('cms.Workflow');

echo Workflow::trigger (
	'error',
	array (
		'message' => $errno . ' ' . $errors[$errno]['title'],
	)
);

header ('HTTP/1.1 ' . $errno . ' ' . $errors[$errno]['title']);

page_title ($errors[$errno]['title']);

switch ($errno) {
	case 401:
		echo '<p>' . intl_get ('You don\'t have the permission to access the requested page.') . '</p>';
		break;
	case 403:
		echo '<p>' . intl_get ('You don\'t have the permission to access the requested page.') . '</p>';
		break;
	case 404:
		echo '<p>' . intl_get ('The page you requested could not be found.') . '</p>';
		break;
	case 500:
		echo '<p>' . intl_get ('The server has encountered an unknown internal error.') . '</p>';
		break;
}

echo '<p>' . intl_get ('Perhaps you might find what you\'re looking for in the list below.') . '</p>';

echo loader_box ('sitellite/nav/sitemap');

?>