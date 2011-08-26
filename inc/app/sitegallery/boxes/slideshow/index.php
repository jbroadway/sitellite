<?php

if (session_admin ()) {
	$acl = session_allowed_sql ();
} else {
	$acl = session_approved_sql ();
}

$res = db_fetch_array (
	'select name, display_title, extension, description from sitellite_filesystem where path = ? and ' . $acl . ' order by name asc',
	$parameters['path']
);

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
	'slideshow.spt',
	array (
		'path' => $parameters['path'],
		'total' => count ($res) + 1,
		'desc' => $parameters['descriptions'],
		'delay' => $parameters['delay'],
	)
);

?>