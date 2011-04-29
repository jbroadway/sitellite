<?php

$data = array ();

// required parameters (href, text, tooltip)

if (! isset ($parameters['href'])) {
	$data['href'] = 'javascript: void (0)';
} else {
	$data['href'] = $parameters['href'];
	unset ($parameters['href']);
}

if (! isset ($parameters['text'])) {
	$data['text'] = '...';
} else {
	$data['text'] = $parameters['text'];
	unset ($parameters['text']);
}

if (! isset ($parameters['tooltip'])) {
	$data['tooltip'] = '...';
} else {
	$data['tooltip'] = '\'' . str_replace ('\'', '\\\'', $parameters['tooltip']) . '\'';
	unset ($parameters['tooltip']);
}

// some defaults (width)

if (! isset ($parameters['width'])) {
	$parameters['width'] = 200;
}

// building parameter list

foreach ($parameters as $key => $value) {
	$data['params'] .= ', ' . strtoupper ($key) . ', ';
	if (is_numeric ($value)) {
		$data['params'] .= $value;
	} else {
		$data['params'] .= '\'' . str_replace ('\'', '\\\'', $value) . '\'';
	}
}

// giv'er

echo template_simple (
	'<a
		href="{href}"
		onmouseover="return overlib ({tooltip}{params})"
		onmouseout="return nd ()">{text}</a>',
	$data
);

?>