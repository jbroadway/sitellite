<?php
$parameters['image'] = urldecode($parameters['image']);
if (strpos ($parameters['image'], '/pix') !== 0) {
	list ($tmp, $parameters['image']) = explode ('/pix', $parameters['image'], 2);
	$parameters['image'] = '/pix' . $parameters['image'];
}

list ($width, $height) = getimagesize (site_docroot () . $parameters['image']);

$float = $parameters['float'];

if (empty ($float)) {
	$float = false;
}

echo template_simple (
	'protected.spt',
	array (
		'image' => $parameters['image'],
		'watermark' => $parameters['watermark'],
		'width' => $width,
		'height' => $height,
		'float' => $float,
	)
);

?>