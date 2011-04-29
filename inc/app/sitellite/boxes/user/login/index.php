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

global $session, $site;

if (isset ($parameters['template'])) {
	page_template ($parameters['template']);
}

if (! empty ($parameters['username'])) {
	sleep (2);
}

if ($session->valid) {
	if (! empty ($parameters['goto'])) {
		header ('Location: ' . $site->url . '/index/' . $parameters['goto']);
		exit;
	} elseif (session_admin ()) {
		header ('Location: ' . $site->url . '/index/cms-app');
		exit;
	} else {
		page_title (intl_get ('Members'));
		echo template_simple ('user/login/home.spt', $parameters);
	}
} else {
	switch ($box['context']) {
		case 'action':
			if (! empty ($parameters['username'])) {
				if (! empty ($parameters['invalid'])) {
					header ('Location: ' . $site->url . '/index/' . $parameters['invalid']);
					exit;
				} else {
					page_title (intl_get ('Invalid Password'));
					echo template_simple ('user/login/invalid.spt', $parameters);
				}
			} else {
				page_title (intl_get ('Members'));
				echo template_simple ('user/login/inline.spt', $parameters);
			}
			break;
		case 'inline':
			page_title (intl_get ('Members'));
			echo template_simple ('user/login/inline.spt', $parameters);
			break;
		case 'normal':
			echo template_simple ('user/login/normal.spt', $parameters);
			break;
	}
}

?>