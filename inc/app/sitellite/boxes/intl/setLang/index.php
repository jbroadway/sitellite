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

global $cookie, $intl;

if ($intl->negotiation == 'cookie') {
	$cookie->set ('sitellite_lang_pref', $parameters['choice'], '', '/');

	if ($parameters['goHome']) {
		header ('Location: ' . site_prefix () . '/index');
	} else {
		header ('Location: ' . $_SERVER['HTTP_REFERER']);
	}
} elseif ($intl->negotiation == 'url') {
    $curLang = $parameters['curlang'];
	if ($parameters['goHome']) {
		if (strpos (site_prefix (), '/' . $curLang . '/') !== false) {
			$prefix = str_replace ('/' . $curLang . '/', '/' . $parameters['choice'] . '/', site_prefix ());
		} else {
			$prefix = site_prefix () . '/' . $parameters['choice'];
		}
		header ('Location: ' . $prefix . '/index');
	}  else {
		if (strpos ($_SERVER['HTTP_REFERER'], '/' . $curLang . '/') !== false) {
			$referrer = str_replace ('/' . $curLang . '/', '/' . $parameters['choice'] . '/', $_SERVER['HTTP_REFERER']);
		} elseif (strpos ($_SERVER['HTTP_REFERER'], '/index') !== false) {
			$referrer = str_replace ('/index', '/' . $parameters['choice'] . '/index', $_SERVER['HTTP_REFERER']);
		} else {
			$info = parse_url ($_SERVER['HTTP_REFERER']);
			if ($info['path'] == '/' || $info['path'] == '/' . $curLang) {
				$referrer = '/' . $parameters['choice'];
			} else {
				$referrer = str_replace ($info['path'], '/' . $parameters['choice'] . $info['path'], $_SERVER['HTTP_REFERER']);
			}
		}
		header ('Location: ' . $referrer);
	}
}

exit;

?>
