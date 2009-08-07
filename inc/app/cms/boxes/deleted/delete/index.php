<?php

global $cgi;

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

$res = $rex->clear ($cgi->key);

if (! $res) {
	die ($rex->error);
}

session_set ('sitellite_alert', intl_get ('The item have been permanently deleted.'));

header ('Location: ' . site_prefix () . '/index/cms-deleted-items-action?collection=' . urlencode ($cgi->collection));
exit;

?>
