<?php

if ($parameters['appname'] == 'GLOBAL') {
	$app = 'GLOBAL';
	$read = 'inc/html';
	$write = 'inc/lang/index.php';
	$list = 'inc/lang/languages.php';
	$langs = 'inc/lang';
	$info = array (
		'app_name' => intl_get ('Global'),
	);
} elseif ($parameters['appname'] == 'SAF') {
	$app = 'SAF';
	$read = 'saf/lib';
	$write = 'inc/lang/saf.php';
	$list = 'inc/lang/languages.php';
	$langs = 'inc/lang';
	$info = array (
		'app_name' => intl_get ('Sitellite Libraries'),
	);
} elseif (empty ($parameters['appname']) || strstr ($parameters['appname'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
} else {
	$app = $parameters['appname'];
	$read = 'inc/app/' . $app;
	$write = 'inc/app/' . $app . '/lang/index.php';
	$list = 'inc/lang/languages.php';
	$langs = 'inc/app/' . $app . '/lang';
	$info = ini_parse (getcwd () . '/inc/app/' . $app . '/conf/config.ini.php', false);
}

if (! @is_dir ($read)) {
	page_title (intl_get ('An Error Occurred'));
	echo intl_get ('No application folder found') . ' (' . $read . ').';
	return;
}

if (! @is_dir ($langs)) {
	$r = @mkdir ($langs, 0777);
	if (! $r) {
		page_title (intl_get ('An Error Occurred'));
		echo intl_get ('No language folder found') . ' (' . $langs . '). ' . intl_get ('Attempt to create failed.');
		return;
	}
}

if (! @is_writeable ($langs)) {
	page_title (intl_get ('An Error Occurred'));
	echo intl_get ('Cannot write to languages folder') . ' (' . $langs . '). ' . intl_get ('Please change your filesystem permissions.');
	return;
}

if (! @file_exists ($list)) {
	loader_import ('saf.File');
	$r = file_overwrite ($list, ini_write (array ()));
	if (! $r) {
		page_title (intl_get ('An Error Occurred'));
		echo intl_get ('No language file found') . ' (' . $list . '). ' . intl_get ('Attempt to create failed.');
		return;
	}
}

if (! @file_exists ($write)) {
	$info = pathinfo ($write);
	if (! @is_writeable ($info['dirname'])) {
		page_title (intl_get ('An Error Occurred'));
		echo intl_get ('Cannot write to language folder') . ' (' . $info['dirname'] . '). ' . intl_get ('Please change your filesystem permissions.');
		return;
	}
} else {
	if (! @is_writeable ($write)) {
		page_title (intl_get ('An Error Occurred'));
		echo intl_get ('Cannot write to the language file') . ' (' . $write . '). ' . intl_get ('Please change your filesystem permissions.');
		return;
	}
}

$fullname = $info['app_name'];
if (! $fullname) {
	$fullname = ucfirst ($app);
}

set_time_limit (0);

page_title (intl_get ('Creating Index') . ' - ' . $fullname);

echo '<p><strong>' . intl_get ('Generating translation index, please wait...') . '</strong></p>';

global $intl;

$list = $intl->build_keylist ($read, $langs);
if (! $list && $intl->error) {
	echo '<p>Error building index: ' . $intl->error . '</p>';
	return;
}

$res = $intl->writeIndex ($write, $list);
if (! $res) {
	echo '<p>Error writing index: ' . $intl->error . '</p>';
	return;
}

echo template_simple ('<p><strong>{intl Finished}.  <a href="{site/prefix}/index/multilingual-translation-action?appname={cgi/appname}">{intl Back}</a></strong></p>');

?>