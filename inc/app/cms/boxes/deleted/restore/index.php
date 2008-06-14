<?php

global $cgi;

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

$res = $rex->restore ($cgi->key, $cgi->rid, array ());

if (! $res) {
	die ($rex->error);
}

header ('Location: ' . site_prefix () . '/index/cms-deleted-items-action?collection=' . urlencode ($cgi->collection) . '&_msg=restored');
exit;

?>