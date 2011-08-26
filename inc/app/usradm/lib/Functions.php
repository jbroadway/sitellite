<?php

function usradm_resource_name ($resource) {
	if (strpos ($resource, 'app_') === 0) {
		if (@file_exists ('inc/app/' . substr ($resource, 4) . '/conf/config.ini.php')) {
			$ini = parse_ini_file ('inc/app/' . substr ($resource, 4) . '/conf/config.ini.php');
			return $ini['app_name'];
		}
		return ucwords (str_replace ('_', ' ', substr ($resource, 4)));
	} elseif (@file_exists ('inc/app/cms/conf/collections/' . $resource . '.php')) {
		$ini = parse_ini_file ('inc/app/cms/conf/collections/' . $resource . '.php', true);
		return $ini['Collection']['display'];
	}
	return ucwords (str_replace ('_', ' ', $resource));
}

?>