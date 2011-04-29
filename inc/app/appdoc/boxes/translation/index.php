<?php

global $cgi;

if ($cgi->appname == 'GLOBAL') {
	$info = array (
		'app_name' => 'Global',
	);
	$lang_path = 'inc/lang';
} elseif (empty ($cgi->appname) || strstr ($cgi->appname, '..') || ! @is_dir ('inc/app/' . $cgi->appname)) {
	header ('Location: ' . site_prefix () . '/index/appdoc-app');
	exit;
} else {
	$info = ini_parse (getcwd () . '/inc/app/' . $cgi->appname . '/conf/config.ini.php', false);
	$lang_path = 'inc/app/' . $cgi->appname . '/lang';
}

page_title (intl_get ('Languages') . ': ' . $info['app_name']);

if (! @is_dir ($lang_path)) {
	loader_import ('saf.File.Directory');

	$res = Dir::build ($lang_path, 0777);

	if (! $res) {
		echo '<p>' . intl_get ('Failed to create directory') . ': lang</p>';
		echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
		return;
	}
}

global $intl;

$data = array (
	'appname' => $cgi->appname,
	'langs' => $intl->getLanguages ($lang_path . '/languages.php'),
);

if (! is_array ($data['langs'])) {
	$data['langs'] = array ();
}

function filter_translation_default ($v) {
	if ($v) {
		return intl_get ('Yes');
	}
	return intl_get ('No');
}

echo template_simple ('translation.spt', $data);

?>
