<?php

loader_import ('saf.File.Directory');

$dir = new Dir (getcwd () . '/inc/app');
if (! $dir->handle) {
	die ($dir->error);
}

$apps = parse_ini_file ('inc/conf/auth/applications/index.php');

$files = $dir->read_all ();

$core = array ();
$list = array ();
foreach ($files as $file) {
	if (strpos ($file, '.') === 0 || $file == 'CVS') {
		continue;
	} elseif (@is_dir (getcwd () . '/inc/app/' . $file)) {
		if (! @file_exists (getcwd () . '/inc/app/' . $file . '/conf/settings.ini.php')) {
			//continue;
			$settings = false;
		} else {
			$settings = true;
		}

		// get name
		$info = ini_parse (getcwd () . '/inc/app/' . $file . '/conf/config.ini.php', false);
		if (isset ($info['app_name'])) {
			$name = $info['app_name'];
		} else {
			$name = ucfirst ($file);
		}

		if (isset ($apps[$file]) && $apps[$file] == 'core') {
			$core[$file] = array (
				'name' => $name,
				'settings' => $settings,
			);
		} else {
			if (! isset ($apps[$file])) {
				$apps[$file] = true;
			}
			$list[$file] = array (
				'name' => $name,
				'settings' => $settings,
				'active' => $apps[$file],
			);
		}
	}
}

asort ($core);
asort ($list);

page_title (intl_get ('Applications'));

if (! is_writeable (getcwd () . '/inc/conf/auth/applications/index.php')) {
	echo '<p class="invalid">' . intl_getf (
		'Warning: The application list file is not writeable. Please verify that the file %s is writeable by the web server user.',
		'inc/conf/auth/applications/index.php'
	) . '</p>';
}

template_simple_register ('core', $core);
echo template_simple ('applications.spt', $list);

//info ($core);
//info ($list);

?>