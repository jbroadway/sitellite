<?php

if (empty ($parameters['appname']) || strstr ($parameters['appname'], '..') || ! @is_dir (getcwd () . '/inc/app/' . $parameters['appname'])) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$app = $parameters['appname'];
$read = 'inc/app/' . $app . '/lib';
$write = 'inc/app/' . $app . '/docs/api';
$docs = 'inc/app/' . $app . '/docs';

if (! @is_dir ($read)) {
	die ('No lib folder found.');
}

if (! @is_dir ($docs)) {
	$r = mkdir ($docs, 0777);
	if (! $r) {
		die ('No docs folder found.  Attempt to create failed.');
	}
}

if (! @is_dir ($write)) {
	$r = mkdir ($write, 0777);
	if (! $r) {
		die ('No docs/api folder found.  Attempt to create failed.');
	}
}

if (! @is_writeable ($write)) {
	die ('Cannot write to docs/api folder.  Please change your filesystem permissions.');
}

$info = ini_parse (getcwd () . '/inc/app/' . $app . '/conf/config.ini.php', false);

$fullname = $info['app_name'];
if (! $fullname) {
	$fullname = ucfirst ($app);
}

set_time_limit (900);

page_title (intl_get ('AppDoc') . ': ' . $fullname);

echo '<p><strong>' . intl_get ('Generating API documentation, please be patient.') . '</strong></p>';

echo '<pre>';

passthru ('./saf/lib/PEAR/PhpDocumentor/phpdoc -d ' . $read . ' -s -t ' . $write . ' -ti "' . $fullname . '" -o HTML:Smarty:Sitellite -dn ' . $app);

echo '</pre>';

echo template_simple ('<p><strong>{intl Finished}.  <a href="{site/prefix}/index/appdoc-appinfo-action?appname={cgi/appname}">{intl Back}</a></strong></p>');

set_time_limit (30);

?>