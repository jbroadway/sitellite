<?php

loader_import ('saf.File.Directory');
loader_import ('appdoc.functions');

if ($parameters['appname'] == 'GLOBAL') {
	header ('Location: ' . site_prefix () . '/index/appdoc-app');
	exit;
} elseif (empty ($parameters['appname']) || strstr ($parameters['appname'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$path = getcwd () . '/inc/app/' . $parameters['appname'];

$info = ini_parse (getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/config.ini.php', false);

$fullname = $info['app_name'];
if (! $fullname) {
	$fullname = ucfirst ($parameters['appname']);
}

$data = array ('boxes' => array (), 'forms' => array ());

help_walker ($parameters['appname'], 'boxes', $path . '/boxes', $data['boxes']);
help_walker ($parameters['appname'], 'forms', $path . '/forms', $data['forms']);

foreach ($info as $k => $v) {
	$data[$k] = $v;
}

if (isset ($data['author'])) {
	$data['author'] = preg_replace ('/<([^>]+)>/', '&lt;<a href="mailto:\1?subject=' . $fullname . '">\1</a>&gt;', $data['author']);
}

if (@is_dir ($path . '/docs/en')) {
	$data['helpdocs'] = site_prefix () . '/index/help-app?appname=' . $parameters['appname'];
} else {
	$data['helpdocs'] = false;
}

if (@file_exists ($path . '/docs/api/index.html')) {
	$data['apidocs'] = site_prefix () . '/inc/app/' . $parameters['appname'] . '/docs/api';
} else {
	$data['apidocs'] = false;
}

// output

page_title (intl_get ('AppDoc') . ': ' . $fullname);

echo template_simple ('appdoc.spt', $data);

?>