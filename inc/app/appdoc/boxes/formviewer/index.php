<?php

loader_import ('appdoc.functions');

if (empty ($parameters['appname']) || strstr ($parameters['appname'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

if (empty ($parameters['form']) || strstr ($parameters['form'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'] . '/forms/' . $parameters['form'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$path = getcwd () . '/inc/app/' . $parameters['appname'] . '/forms/' . $parameters['form'];
$data = array ();

$data['access'] = help_get_access ($path, getcwd () . '/inc/app/' . $parameters['appname']);
$data['settings'] = ini_parse ($path . '/settings.php');
$data['info'] = array_shift ($data['settings']);
$data['params'] = $data['settings'];
$data['description'] = $data['info']['description'];

foreach ($data['params'] as $key => $value) {
	$rules = array ();
	foreach ($value as $k => $v) {
		if (strpos ($k, 'rule ') === 0) {
			$rules[] = $v;
		}
	}
	if (count ($rules) == 0) {
		$rules = false;
	}
	$data['params'][$key]['rules'] = $rules;
}

ob_start ();
highlight_file ($path . '/index.php');
$data['source'] = ob_get_contents ();
ob_end_clean ();

$info = ini_parse (getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/config.ini.php', false);

$fullname = $info['app_name'];
if (! $fullname) {
	$fullname = ucfirst ($parameters['appname']);
}

$pathinfo = explode ('/', $parameters['form']);

foreach ($pathinfo as $key => $value) {
	$pathinfo[$key] = ucfirst ($value);
}

if (! empty ($data['info']['name'])) {
	$pathinfo[count ($pathinfo) - 1] = $data['info']['name'];
}

$name = join (' / ', $pathinfo);

page_title (intl_get ('AppDoc') . ': ' . $fullname . ' / ' . $name);

echo template_simple ('<p><a href="{site/prefix}/index/appdoc-appinfo-action?appname={cgi/appname}">{intl Back}</a></p>');

echo template_simple ('formviewer.spt', $data);

?>