<?php

loader_import ('saf.File');

if (isset ($parameters['ext'])) {
	$ext = preg_split ('/, ?/', $parameters['ext']);
} else {
	$ext = false;
}

$file = file_rand ($parameters['path'], $ext);

if (! $file) {
	return;
}

if ($parameters['img'] == 'no') {
	echo '/' . $parameters['path'] . '/' . $file;
} else {
	echo '<img src="/' . $parameters['path'] . '/' . $file . '" alt="" border="0" />';
}

?>