<?php

$sites = array (
	'digg.com',
	'www.digg.com',
	'slashdot.org',
	'reddit.com',
	'fark.com',
	'somethingawful.com',
	'kuro5hin.org',
	'engadget.com',
	'boingboing.net',
	'del.icio.us',
	'netscape.com',
);

if (isset ($_SERVER['HTTP_REFERER']) && ! strstr ($_SERVER['HTTP_USER_AGENT'], 'CoralWebPrx')) {
	$referer = parse_url ($_SERVER['HTTP_REFERER']);
	$referer = $referer['host'];
	if (in_array ($referer, $sites)) {
		header ('Location: http://' . $_SERVER['HTTP_HOST'] . '.nyud.net:8080' . $_SERVER['REQUEST_URI']);
		exit;
	}
}

?>