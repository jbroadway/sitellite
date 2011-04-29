<?php

global $cgi;

if (! $cgi->appname) {
	page_title (intl_get ('Translations') . ' - ' . intl_get ('Applications'));

	loader_import ('saf.File.Directory');
	
	$dir = new Dir (getcwd () . '/inc/app');
	if (! $dir->handle) {
		die ($dir->error);
	}
	
	$apps = array ();
	$files = $dir->read_all ();

	$list = array ();
	foreach ($files as $file) {
		if (strpos ($file, '.') === 0 || $file == 'CVS') {
			continue;
		} elseif (@is_dir (getcwd () . '/inc/app/' . $file)) {
			// get name
			$info = ini_parse (getcwd () . '/inc/app/' . $file . '/conf/config.ini.php', false);
			if (isset ($info['app_name'])) {
				$name = $info['app_name'];
			} else {
				$name = ucfirst ($file);
			}
			$list[$file] = $name;
		}
	}
	$list['SAF'] = intl_get ('Sitellite Libraries');
	asort ($list);

	echo template_simple (
		'<p><a href="{site/prefix}/index/multilingual-app">{intl Back}</a></p>
		<h2>{intl Choose an application}</h2>
		<ul>
		{loop obj}
			<li><a href="{site/prefix}/index/multilingual-translation-action?appname={loop/_key}">{loop/_value}</a></li>
		{end loop}
		</ul>',
		$list
	);

	return;
}

if ($cgi->appname == 'GLOBAL') {
	$info = array (
		'app_name' => intl_get ('Global'),
	);
	$lang_path = 'inc/lang';
} elseif ($cgi->appname == 'SAF') {
	$info = array (
		'app_name' => intl_get ('Sitellite Libraries'),
	);
	$lang_path = 'inc/lang';
} elseif (empty ($cgi->appname) || strstr ($cgi->appname, '..') || ! @is_dir ('inc/app/' . $cgi->appname)) {
	header ('Location: ' . site_prefix () . '/index/multilingual-app');
	exit;
} else {
	$info = ini_parse (getcwd () . '/inc/app/' . $cgi->appname . '/conf/config.ini.php', false);
	$lang_path = 'inc/lang';
}

if ($cgi->appname == 'GLOBAL') {
	page_title (intl_get ('Translations') . ' - ' . intl_get ('Global Templates'));
} else {
	page_title (intl_get ('Translations') . ' - ' . $info['app_name']);
}

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

if (!isset ($info['language'])) {
	$info['language'] = 'en';
}

$data = array (
	'appname' => $cgi->appname,
	'langs' => $intl->getLanguages ($lang_path . '/languages.php'),
	'app_lang' => $info['language'],
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
