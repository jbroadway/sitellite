<?php

global $cgi;

if (empty ($cgi->_table)) {
	header ('Location: ' . site_prefix () . '/index/myadm-app');
	exit;
}

$tbl = db_table ($cgi->_table);
$pkey = $tbl->pkey;

foreach ($cgi->_key as $key) {
	$res = db_execute ('delete from ' . $cgi->_table . ' where ' . $pkey . ' = ?', $key);
}

//if (! $res) {
//	die (db_error ());
//} else {
	page_title ( 'Database Manager - Deleted items from "' . $cgi->_table . '"' );
	echo template_simple ('<p><a href="{site/prefix}/index/myadm-browse-action?table=' . urlencode ($cgi->_table) . '">Back</a></p>');
//}

//exit;

?>