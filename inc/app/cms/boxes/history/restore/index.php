<?php

global $cgi;

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

$res = $rex->restore ($cgi->key, $cgi->rid, array (), '', true);

if (! $res) {
	die ($rex->error);
}

if (! empty ($cgi->_return)) {
	header ('Location: ' . $cgi->_return);
} else {
	header ('Location: ' . site_prefix () . '/index/' . $cgi->key);
}
exit;

?>