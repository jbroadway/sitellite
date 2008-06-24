<?php

loader_import ('saf.File.Directory');
loader_import ('monoslideshow.Functions');

if (! empty ($parameters['folder'])) {
	$folder = 'pix/' . $parameters['folder'];
} else {
	$folder = 'pix';
}
$images = Dir::find ('*.jpg', $folder);

$parameters['images'] = array ();

$parameters['album_thumb'] = false;

foreach ($images as $image) {
	$thumb = monoslideshow_thumbnail ($image);
	$parameters['images'][] = array ('image' => $image, 'thumb' => $thumb);
	if (! $parameters['album_thumb']) {
		$parameters['album_thumb'] = $thumb;
	}
}

//info ($parameters);
//exit;

header ('Content-Type: text/xml');
echo template_simple ('xml.spt', $parameters);
exit;

?>