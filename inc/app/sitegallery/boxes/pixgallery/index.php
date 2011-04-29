<script>
<!--
jQuery.noConflict();
-->

</script>
<?php

loader_import ('saf.File.Directory');
loader_import ('sitegallery.Functions');

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

if ($parameters['title']) {
	if ($box['context'] == 'action') {
		page_title ($parameters['title']);
	} else {
		echo '<h2>' . $parameters['title'] . '</h2>';
	}
	if (appconf ('page_alias')) {
		page_id ('sitegallery-pixgallery-action');
		page_below (appconf ('page_alias'));
	}
}

$valid = appconf ('valid');

foreach (array_keys ($res) as $k) {
	if (! in_array (strtolower ($res[$k]->extension), $valid)) {
		unset ($res[$k]);
		continue;
	}
	$res[$k]->src = sitegallery_get_thumbnail ($parameters['path'] . '/' . $res[$k]->name . '.' . $res[$k]->extension);
	//list ($res[$k]->width, $res[$k]->height) = getimagesize ('pix/' . $parameters['path'] . '/' . $res[$k]->name . '.' . $res[$k]->extension);
}

template_simple_register ('results', $res);

page_add_script (site_prefix () . '/inc/app/sitegallery/js/prototype.js');
page_add_script (site_prefix () . '/inc/app/sitegallery/js/scriptaculous.js?load=effects,builder');
page_add_script (site_prefix () . '/inc/app/sitegallery/js/lightbox.js');
page_add_style (site_prefix () . '/inc/app/sitegallery/html/lightbox.css');

echo template_simple (
	'pixgallery.spt',
	array (
		'path' => $parameters['path'],
		'total' => count ($res) + 1,
		'desc' => $parameters['descriptions'], 
	)
);

?>