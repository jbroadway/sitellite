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

if (! isset ($parameters['prev'])) {
	$parameters['prev'] = '<strong>Previous:</strong> <a href="{site/prefix}/index/{id}">{title}</a>';
}

if (! isset ($parameters['next'])) {
	$parameters['next'] = '<strong>Next:</strong> <a href="{site/prefix}/index/{id}">{title}</a>';
}

if (! isset ($parameters['up'])) {
	$parameters['up'] = '<strong>Up:</strong> <a href="{site/prefix}/index/{id}">{title}</a>';
}

if (! isset ($parameters['toc'])) {
	$parameters['toc'] = 'Table of Contents';
}

if (! isset ($parameters['skipIndex'])) {
	$parameters['skipIndex'] = 'yes';
}

$links = array (
	'prev' => false,
	'next' => false,
	'up' => false,
);

if (! empty ($page->below_page)) {
	$links['up'] = template_simple ($parameters['up'], $menu->{'items_' . $page->below_page});
} else {
	$links['up'] = template_simple ($parameters['up'], array ('id' => '', 'title' => $parameters['toc']));
}

if (is_object ($menu->{'items_' . $page->id}->parent)) {
	$list =& $menu->{'items_' . $page->id}->parent->children;
} else {
	$list =& $menu->tree;
}

$pobj = false;
$nobj = false;
$found = false;

foreach ($list as $key => $item) {
	if ($parameters['skipIndex'] == 'yes' && $item->id == 'index') {
		continue;
	} elseif ($item->id != $page->id) {
		if ($found) {
			$nobj =& $list[$key];
			break;
		} else {
			$pobj =& $list[$key];
		}
	} else {
		// we've got pobj now (or know it's false)
		$found = true;
	}
}
		
if (is_object ($pobj)) {
	$links['prev'] = template_simple ($parameters['prev'], $pobj);
}
		
if (is_object ($nobj)) {
	$links['next'] = template_simple ($parameters['next'], $nobj);
}

echo template_simple ('<p>{prev} {up} {next}</p>', $links) . NEWLINE;

?>