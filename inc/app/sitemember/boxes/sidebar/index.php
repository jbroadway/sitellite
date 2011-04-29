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

if (session_valid ()) {
	$sidebar = appconf ('sidebar');
	if ($sidebar == 'default') {
		echo template_simple ('sidebar.spt', $parameters);
	} else {
		list ($type, $call) = split (':', $sidebar);
		$func = 'loader_' . $type;
		echo $func (trim ($call), array (), $box['context']);
	}
} else {
	echo loader_box ('sitemember/login', $parameters);
}

?>