<?php

if (! $parameters['description']) {
	$parameters['description'] = 'yes';
}

if (! $parameters['delay']) {
	$parameters['delay'] = 12;
}

loader_import ('saf.File.Directory');

$d = new Dir ('pix/' . $parameters['path']);

$res = array ();

foreach ($d->readAll () as $file) {
	if (strpos ($file, '.') === 0) {
		continue;
	}
	$info = pathinfo ($file);
	$info['name'] = preg_replace ('/\.' . $info['extension'] . '$/', '', $info['basename']);
	list ($info['display_title'], $info['description']) = split (' - ', $info['name']);
	$info['display_title'] = ucwords (preg_replace ('/[^a-zA-Z0-9]+/', ' ', $info['display_title']));
	unset ($info['dirname']);
	unset ($info['basename']);
	if (empty ($info['description'])) {
		$info['description'] = $info['display_title'];
	}
	$res[] = (object) $info;
}

$valid = appconf ('valid');

foreach (array_keys ($res) as $k) {
	if (! in_array (strtolower ($res[$k]->extension), $valid)) {
		unset ($res[$k]);
	}
}

if ($parameters['title']) {
	if ($box['context'] == 'action') {
		page_title ($parameters['title']);
	} else {
		echo '<h2>' . $parameters['title'] . '</h2>';
	}
}

page_add_script (site_prefix () . '/js/rollover.js');

template_simple_register ('results', $res);
template_simple_register ('first', array_shift ($res));

echo template_simple (
	'pixslideshow.spt',
	array (
		'path' => $parameters['path'],
		'total' => count ($res) + 1,
		'desc' => $parameters['descriptions'],
		'delay' => $parameters['delay'],
	)
);

?>