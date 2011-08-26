<?php

loader_box ('sitellite/nav/init');

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

// import any object we need from the global namespace
global $page, $menu;

// box logic begins here

if ($parameters['recursive'] == 'yes') {
	$recur = true;
} else {
	$recur = false;
}

if (empty ($parameters['root'])) {
	$sec = page_get_section ();
	if (! empty ($sec)) {
		$parameters['root'] = $sec;
	}
}

if (! empty ($parameters['root']) && is_object ($menu->{'items_' . $parameters['root']})) {
	echo '<ul>';
	foreach (array_keys ($menu->{'items_' . $parameters['root']}->children) as $k) {
		echo $menu->{'items_' . $parameters['root']}->children[$k]->display ('html', '<a href="{site/prefix}/index/{id}">{title}</a>', $recur);
	}
	echo '</ul>';
}

?>
