<?php


if ($parameters['collection'] == 'sitellite_page' || $parameters['collection'] == 'sitellite_sidebar') {
	global $cache;	
	if($cache)
	  $cache->clear (); 
}

?>