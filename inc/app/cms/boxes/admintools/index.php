<?php

if (! session_admin ()) {
	return;
}

$applications = parse_ini_file ('inc/conf/auth/applications/index.php');

loader_import ('saf.File.Directory');
$d = new Dir ('inc/app');

$apps = array ();

foreach ($d->read_all () as $file) {
	if (
			strpos ($file, '.') === 0 ||
			! @is_dir ('inc/app/' . $file) ||
			! @file_exists ('inc/app/' . $file . '/conf/config.ini.php') ||
			in_array ($file, array ('cms', 'usradm'))
	) {
		continue;
	}

	if (session_is_resource ('app_' . $file) && ! session_allowed ('app_' . $file, 'rw', 'resource')) {
		continue;
	}

	if (isset ($applications[$file]) && ! $applications[$file]) {
		continue;
	}

	$c = @parse_ini_file ('inc/app/' . $file . '/conf/config.ini.php');
	if (! isset ($c['admin_handler']) || ! isset ($c['admin_handler_type']) || (isset ($c['admin']) && ! $c['admin'])) {
		continue;
	}
	if (! isset ($c['app_name'])) {
		$c['app_name'] = $file;
	}
	if ($c['admin_handler_type'] == 'box') {
		$type = 'action';
	} else {
		$type = $c['admin_handler_type'];
	}

	if ($type == 'app') {
		$apps[$c['app_name']] = $file . '-' . $type;
	} else {
		$apps[$c['app_name']] = $file . '-' . $c['admin_handler'] . '-' . $type;
	}
}

if ($box['context'] == 'action') {
	page_title (intl_get ('Admin Tools'));
}

ksort ($apps);

if ($box['context'] == 'action') {
	echo template_simple ('admintools.spt', array ('apps' => $apps));
} else {
	foreach ($apps as $k => $v) {
		echo $k . TAB . $v . NEWLINE;
	}
}

?>