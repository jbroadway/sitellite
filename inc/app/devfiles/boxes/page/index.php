<?php

if ($parameters['files'] == 'yes') {
	$anchor = '#files';
} else {
	$anchor = '';
}

if (strpos ($parameters['appname'], '.') === false || strpos ($parameters['appname'], '/') === false) {
	header ('Location: ' . site_prefix () . '/index/' . $parameters['appname'] . $anchor);
} else {
	header ('Location: ' . $parameters['appname'] . $anchor);
}

exit;

?>