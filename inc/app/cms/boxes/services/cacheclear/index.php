<?php

if ($parameters['collection'] == 'sitellite_page' || $parameters['collection'] == 'sitellite_sidebar') {
	global $cache;
	$cache->clear ();
}

?>