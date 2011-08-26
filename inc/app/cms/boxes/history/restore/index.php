<?php

global $cgi;

loader_import ('cms.Versioning.Rex');
loader_import ('cms.Workflow');

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

$res = $rex->restore ($cgi->key, $cgi->rid, array (), '', true);

if (! $res) {
	die ($rex->error);
}

echo Workflow::trigger (
        'restore',
        array (
                'collection' => $cgi->collection,
                'key'        => $cgi->key,
                'message'    => 'Collection: ' . $cgi->collection . ', Item: ' .$cgi->key,
        )
);

if (! empty ($cgi->_return)) {
	header ('Location: ' . $cgi->_return);
} else {
	header ('Location: ' . site_prefix () . '/index/' . $cgi->key);
}
exit;

?>
