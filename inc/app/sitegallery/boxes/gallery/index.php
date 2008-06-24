<?php

loader_import ('sitegallery.Functions');

if (session_admin ()) {
	$acl = session_allowed_sql ();
} else {
	$acl = session_approved_sql ();
}

$res = db_fetch_array (
	'select name, display_title, extension, description from sitellite_filesystem where path = ? and ' . $acl . ' order by name asc',
	$parameters['path']
);

if ($parameters['title']) {
	if ($box['context'] == 'action') {
		page_title ($parameters['title']);
	} else {
		echo '<h2>' . $parameters['title'] . '</h2>';
	}
}

$valid = appconf ('valid');

foreach (array_keys ($res) as $k) {
	if (! in_array (strtolower ($res[$k]->extension), $valid)) {
		unset ($res[$k]);
		continue;
	}
	$res[$k]->src = sitegallery_get_thumbnail ($parameters['path'] . '/' . $res[$k]->name . '.' . $res[$k]->extension, true);
	//list ($res[$k]->width, $res[$k]->height) = getimagesize ('inc/data/' . $parameters['path'] . '/' . $res[$k]->name . '.' . $res[$k]->extension);
}

template_simple_register ('results', $res);

page_add_script (site_prefix () . '/inc/app/sitegallery/js/prototype.js');
page_add_script (site_prefix () . '/inc/app/sitegallery/js/scriptaculous.js?load=effects');
page_add_script (site_prefix () . '/inc/app/sitegallery/js/lightbox.js');
page_add_style (site_prefix () . '/inc/app/sitegallery/html/lightbox.css');

echo template_simple (
	'gallery.spt',
	array (
		'path' => $parameters['path'],
		'total' => count ($res) + 1,
		'desc' => $parameters['descriptions'], 
	)
);

?>