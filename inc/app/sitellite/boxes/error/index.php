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

// if there's an old_links.txt in the site root, it will map
// 404 errors from an old site to new pages in Sitellite. the
// format for the file is tab-delimited as follows:
// /new-page-link	/old-page.html
if (@file_exists ('old_links.txt')) {
	$pages = file ('old_links.txt');
	
	foreach ($pages as $key => $value) {
		list ($id, $old) = explode ("\t", $value);
		$old = trim ($old);
	
		if ($_SERVER['REQUEST_URI'] == $old || $_SERVER['REQUEST_URI'] == '/index' . $old) {
			header ('HTTP/1.1 301 Moved Permanently');
			header ('Location: ' . $id);
			exit;
		}
	}
}

// import any object we need from the global namespace
global $errno, $cgi;

// make sure we switch back to html output mode
$cgi->mode = 'html';

// box logic begins here

$errors = conf ('errors');

if (! $errno) {
	$errno = $cgi->code;
}

loader_import ('cms.Workflow');

echo Workflow::trigger (
	'error',
	array (
		'message' => $errno . ' ' . $errors[$errno]['title'] . ': ' . site_current () . ', referrer: ' . $_SERVER['HTTP_REFERER'],
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