<?php

loader_import ('cms.Versioning.Rev');

$rev = new Rev; // default: database, database

unset ($parameters['submit_button']);
unset ($parameters['param']);
unset ($parameters['files']);
unset ($parameters['error']);
unset ($parameters['page']);
unset ($parameters['mode']);

$changelog = $parameters['changelog'];
unset ($parameters['changelog']);

$method = $rev->determineAction ('sitellite_page', 'id', $parameters['id'], $parameters['sitellite_status']);

$res = $rev->{$method} ('sitellite_page', 'id', $parameters['id'], $parameters, $changelog);

if (! $res) {
	die ($rev->error);
} else {
	header ('Location: ' . site_prefix () . '/index/' . $parameters['id']);
	exit;
}

?>