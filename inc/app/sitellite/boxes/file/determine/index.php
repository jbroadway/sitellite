<?php

loader_import ('saf.File');

$list = preg_split ('/, ?/', $parameters['list']);

if (isset ($parameters['path'])) {
	$path = $parameters['path'];
} else {
	$path = '';
}

$file = file_determine ($list, $path);

if (! $file) {
	return;
}

if (! empty ($path)) {
	$path = '/' . $path . '/';
} else {
	$path = '/';
}

if ($parameters['img'] == 'no') {
	echo $path . $file;
} else {
	echo '<img src="' . $path . $file . '" alt="" border="0" />';
}

?>