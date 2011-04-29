<?php

$info = parse_ini_file ('inc/conf/auth/applications/index.php');
if (! empty ($parameters['appname']) && isset ($info[$parameters['appname']]) && $info[$parameters['appname']] != 'core') {
	$info[$parameters['appname']] = true;
	loader_import ('saf.File');
	loader_import ('saf.Misc.Ini');
	file_overwrite ('inc/conf/auth/applications/index.php', ini_write ($info));
}

header ('Location: ' . site_prefix () . '/index/usradm-applications-action');
exit;

?>