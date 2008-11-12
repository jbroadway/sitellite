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

$on = appconf ('login');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'box:sitemember/login') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), $parameters, $context);
	return;
}

if (! empty ($parameters['username'])) {
	sleep (2);
}

$parameters['context'] = $box['context'];

switch ($box['context']) {
	case 'inline':
		echo template_simple ('login/inline.spt', $parameters);
		break;
	case 'action':
		page_title (intl_get ('Members'));
	case 'normal':
	default:
		echo template_simple ('login/normal.spt', $parameters);
		break;
}

?>
