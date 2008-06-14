<?php

loader_import ('saf.File.Directory');
	
$dir = new Dir (getcwd () . '/inc/app');
if (! $dir->handle) {
	die ($dir->error);
}

$files = $dir->read_all ();

$list = array ();
foreach ($files as $file) {
	if (strpos ($file, '.') === 0 || $file == 'CVS') {
		continue;
	} elseif (@is_dir (getcwd () . '/inc/app/' . $file)) {
		if (! @file_exists (getcwd () . '/inc/app/' . $file . '/conf/settings.ini.php')) {
			continue;
		}

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

asort ($list);

page_title (intl_get ('Application Settings'));

echo template_simple ('applications.spt', $list);

?>