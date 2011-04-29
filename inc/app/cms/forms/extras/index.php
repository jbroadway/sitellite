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

global $cgi;

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($cgi->_collection);

if (! $rex->collection) {
	page_title (intl_get ('Error: Collection not found!'));
	echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">' . intl_get ('Back') . '</a></p>';
	return;
}

if (isset ($rex->info['Collection']['edit_extras'])) {
	list ($call, $name) = explode (':', $rex->info['Collection']['edit_extras']);
	if ($call == 'box') {
		echo loader_box ($name);
	} elseif ($call == 'form') {
		echo loader_form ($name);
	} else {
		echo loader_form ($call);
	}
	return;

} else {
	page_title (intl_get ('Error: Collection not found!'));
	echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">' . intl_get ('Back') . '</a></p>';
	return;
}

?>