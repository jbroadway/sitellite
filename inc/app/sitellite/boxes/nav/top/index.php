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
global $menu, $page;

// box logic begins here

$xpos	= preg_split ('/, ?/', $parameters['xpos']);
$ypos	= preg_split ('/, ?/', $parameters['ypos']);
$width	= preg_split ('/, ?/', $parameters['width']);
$count	= 0;

if (! isset ($parameters['separator'])) {
	$parameters['separator'] = '<br />';
}

if (! isset ($parameters['list'])) {
	$list =& $menu->tree;
} else {
	$list = array ();
	foreach (preg_split ('/, ?/', $parameters['list']) as $title) {
		$list[] =& $menu->{'items_' . $title};
	}
}

if (! isset ($parameters['skip'])) {
	$skip = array ();
} else {
	$skip = preg_split ('/, ?/', $parameters['skip']);
}

if ($parameters['dropmenus'] == 'yes') {
	function sdm_filter_underscore ($id) {
		return str_replace ('_', '-', $id);
	}

	$template = '<a class="menu" href="{site/prefix}/index/{id}"{active|none} onmouseover="sdmShowAndHide(\'{filter sdm_filter_underscore}{id}{end filter}\')">{title}</a>';
} else {
	$template = '<a class="menu" href="{site/prefix}/index/{id}"{active|none}>{title}</a>';
}

if ($parameters['sort'] == 'reverse') {
	$list = array_reverse ($list);
}

$sep = '';

foreach ($list as $item) {
	if (in_array ($item->id, $skip)) {
		continue;
	}
	echo $sep;
	$sep = $parameters['separator'];
	if ($page->id == $item->id) {
		$item->active .= ' id="active"';
		echo template_simple ($template, $item);
		$item->id = $page->id;
	} else {
		$item->active = '';
		echo template_simple ($template, $item);
	}

	if ($parameters['dropmenus'] == 'yes') {
		// drop menu
		$params = array (
			'top'	=> $item->id,
			'xpos'	=> $xpos[$count],
			'ypos'	=> $ypos[$count]
		);

		if (isset ($width[$count])) {
			$params['width'] = $width[$count];
		}

		if (isset ($parameters['over'])) {
			$params['over'] = $parameters['over'];
		}

		if (isset ($parameters['out'])) {
			$params['out'] = $parameters['out'];
		}

		if (isset ($parameters['levels'])) {
			$params['levels'] = $parameters['levels'];
		}

		if (isset ($parameters['bgcolor'])) {
			$params['bgcolor'] = $parameters['bgcolor'];
		}

		echo loader_box ('sitellite/nav/dropmenu', $params);
	}

	$count++;
}

?>