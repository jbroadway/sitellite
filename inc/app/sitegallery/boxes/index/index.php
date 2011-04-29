<?php

function sitegallery_count_images ($path) {
	loader_import ('saf.File.Directory');
	$images = 0;
	$valid = appconf ('valid');
	$files = Dir::fetch ($path, true);
	foreach ($files as $file) {
		$info = pathinfo ($file);
		if (in_array ($info['extension'], $valid)) {
			$images++;
		}
	}
	return $images;
}

function sitegallery_first_image ($path) {
	loader_import ('saf.File.Directory');
	$valid = appconf ('valid');
	$files = Dir::fetch ($path, true);
	foreach ($files as $file) {
		$info = pathinfo ($file);
		if (in_array ($info['extension'], $valid)) {
			$path = preg_replace ('|^pix/|', '', $path);
			return $path . '/' . $file;
		}
	}
	return false;
}

function sitegallery_sort_galleries ($a, $b) {
	if ($a['ts'] == $b['ts']) {
		return 0;
	}
	return ($a['ts'] > $b['ts']) ? -1 : 1;
}

function sitegallery_filter_date ($ts) {
	return localdate ('F, Y', $ts);
}

// your app begins here
if (! isset ($parameters['path']) || empty ($parameters['path']) || strpos ($parameters['path'], '..') !== false) {
	$gr = appconf ('gallery_root');
	if (! empty ($gr)) {
		$path = 'pix/' . appconf ('gallery_root');
	} else {
		$path = 'pix';
	}
	$parameters['path'] = appconf ('gallery_root');
} else {
	$path = 'pix/' . $parameters['path'];
}

loader_import ('saf.File.Directory');
loader_import ('sitegallery.Functions');
loader_import ('saf.Database.PropertySet');

$files = Dir::fetch ($path, true);
$galleries = array ();
if (! empty ($parameters['path'])) {
	$prefix = $parameters['path'] . '/';
} else {
	$prefix = '';
}

if (intl_lang () == intl_default_lang ()) {
	$ps1 = new PropertySet ('sitegallery', 'album_title');
	$ps2 = new PropertySet ('sitegallery', 'album_description');
	$ps3 = new PropertySet ('sitegallery', 'album_date');
} else {
	$ps1 = new PropertySet ('sitegallery', 'album_title_' . intl_lang ());
	$ps2 = new PropertySet ('sitegallery', 'album_description_' . intl_lang ());
	$ps3 = new PropertySet ('sitegallery', 'album_date');
}

foreach ($files as $k => $v) {
	if (! @is_dir ($path . '/' . $v)) {
		continue;
	}
	$galleries[] = array (
		'path' => $prefix . $v,
		'name' => ucwords (preg_replace ('/[^a-zA-Z\'-]+/', ' ', $v)),
		'ts' => filemtime ($path . '/' . $v),
		'count' => sitegallery_count_images ($path . '/' . $v),
		'thumb' => sitegallery_get_thumbnail (sitegallery_first_image ($path . '/' . $v)),
		'desc' => '',
	);
	$name = $ps1->get ($prefix . $v);
	if (! empty ($name)) {
		$galleries[count ($galleries) - 1]['name'] = $name;
	}
	$desc = $ps2->get ($prefix . $v);
	if (! empty ($desc)) {
		$galleries[count ($galleries) - 1]['desc'] = nl2br ($desc);
	}
	$date = $ps3->get ($prefix . $v);
	if (! empty ($date)) {
		$galleries[count ($galleries) - 1]['ts'] = strtotime ($date);
	}
}

usort ($galleries, 'sitegallery_sort_galleries');

if ($context == 'action') {
	page_title (appconf ('title'));
}

echo template_simple ('galleries.spt', $galleries);

//info ($galleries);

?>