<?php

if ($parameters['appname'] == 'GLOBAL') {
	$app = 'GLOBAL';
	$read = 'inc/html';
	$write = 'inc/lang/index.php';
	$list = 'inc/lang/languages.php';
	$langs = 'inc/lang';
	$info = array (
		'app_name' => 'Global',
	);
} elseif (empty ($parameters['appname']) || strstr ($parameters['appname'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
} else {
	$app = $parameters['appname'];
	$read = 'inc/app/' . $app;
	$write = 'inc/app/' . $app . '/lang/index.php';
	$list = 'inc/app/' . $app . '/lang/languages.php';
	$langs = 'inc/app/' . $app . '/lang';
	$info = ini_parse (getcwd () . '/inc/app/' . $app . '/conf/config.ini.php', false);
}

if (! @is_dir ($read)) {
	die ('No app folder found.');
}

if (! @is_dir ($langs)) {
	$r = mkdir ($langs, 0777);
	if (! $r) {
		die ('No lang folder found.  Attempt to create failed.');
	}
}

if (! @is_writeable ($langs)) {
	die ('Cannot write to langs folder.  Please change your filesystem permissions.');
}

if (! @file_exists ($list)) {
	loader_import ('saf.File');
	$r = file_overwrite ($list, ini_write (array ()));
	if (! $r) {
		die ('No lang/languages.php file found.  Attempt to create failed.');
	}
}

if (! @file_exists ($write)) {
	$info = pathinfo ($write);
	if (! @is_writeable ($info['dirname'])) {
		die ('Cannot write to lang folder.  Please change your filesystem permissions.');
	}
} else {
	if (! @is_writeable ($write)) {
		die ('Cannot write to lang/en.php file.  Please change your filesystem permissions.');
	}
}

$fullname = $info['app_name'];
if (! $fullname) {
	$fullname = ucfirst ($app);
}

set_time_limit (900);

page_title (intl_get ('Languages') . ': ' . $fullname);

echo '<p><strong>' . intl_get ('Generating translation index, please be patient.') . '</strong></p>';

global $intl;

$list = $intl->build_keylist ($read, $langs);
if (! $list) {
	die ('Error building index: ' . $intl->error);
}

$res = $intl->writeIndex ($write, $list);
if (! $res) {
	die ('Error writing index: ' . $intl->error);
}

echo template_simple ('<p><strong>{intl Finished}.  <a href="{site/prefix}/index/appdoc-translation-action?appname={cgi/appname}">{intl Back}</a></strong></p>');

set_time_limit (30);

?>