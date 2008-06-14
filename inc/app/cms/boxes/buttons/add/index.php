<?php

global $page, $cgi;

if (! session_admin ()) {
	return;
}

if (! isset ($parameters['collection'])) {
	$parameters['collection'] = 'sitellite_page';
}

if (! session_allowed ('add', 'rw', 'resource')) {
	return;
}

if (session_is_resource ($parameters['collection']) && ! session_allowed ($parameters['collection'], 'rw', 'resource')) {
	return;
}

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($parameters['collection']);

if (! $rex->collection) {
	return;
}

$parameters['type'] = intl_get ($rex->info['Collection']['singular']);

echo template_simple ('buttons/add.spt', $parameters);

?>