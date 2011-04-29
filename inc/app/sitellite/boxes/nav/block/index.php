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

loader_box ('sitellite/nav/init');

// import any object we need from the global namespace
global $page, $menu;

// box logic begins here

if ($parameters['recursive'] == 'yes') {
	$recur = true;
} else {
	$recur = false;
}

$parameters['root'] = page_get_section ();

if (! empty ($parameters['root']) && is_object ($menu->{'items_' . $parameters['root']})) {
	echo '<blockquote>';

	// top level
	foreach (array_keys ($menu->{'items_' . $parameters['root']}->children) as $k) {
		echo template_simple ('<a href="/index/{id}">{title}</a><br />', $menu->{'items_' . $parameters['root']}->children[$k]);

		if (page_is_parent ($menu->{'items_' . $parameters['root']}->children[$k]->id) || $menu->{'items_' . $parameters['root']}->children[$k]->id == $page->id) {
			if (count ($menu->{'items_' . $parameters['root']}->children[$k]->children) > 0) {
				echo '<blockquote>';
				foreach (array_keys ($menu->{'items_' . $parameters['root']}->children[$k]->children) as $c) {
					echo template_simple ('<a href="/index/{id}">{title}</a><br />', $menu->{'items_' . $parameters['root']}->children[$k]->children[$c]);
				}
				echo '</blockquote>';
			}
		}
	}

	echo '</blockquote>';
} else {
	echo '<blockquote>';
	foreach (array_keys ($menu->tree) as $k) {
		echo template_simple ('<a href="/index/{id}">{title}</a><br />', $menu->tree[$k]);
	}
	echo '</blockquote>';
}

?>