<?php

if ($parameters['collection'] == 'sitellite_page') {
	global $cache;
	if ($parameters['key'] == 'index') {
		$cache->expire (site_prefix () . '/index');
	}
	$cache->expire (site_prefix () . '/index/' . $parameters['key']);
}

?>