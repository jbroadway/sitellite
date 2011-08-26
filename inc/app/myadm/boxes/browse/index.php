<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #177 Pagination.
//

global $cgi;

if (empty ($cgi->table)) {
	header ('Location: ' . site_prefix () . '/index/myadm-app');
	exit;
}

page_title ( 'Database Manager - Browsing "' . $cgi->table . '"' );




$res = db_fetch ('show tables');
if (! $res) {
	die (db_error ());
} elseif (is_object ($res)) {
	$res = array ($res);
}

$tables = array ();
foreach ($res as $row) {
	$tables[] = $row->{array_shift (array_keys (get_object_vars ($row)))};
}

if (in_array ($cgi->table . '_sv', $tables) || preg_match ('/_sv$/', $cgi->table)) {
	echo '<p style="color: #900; font-weight: bold">' . intl_get ('Warning: Modifying this table directly could cause strange behaviour and even errors in the software.') . '</p>';
}




loader_import ('saf.GUI.Pager');
loader_import ('saf.Misc.TableHeader');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

// Start: SEMIAS #177 Pagination.
// ------------------
//$pg = new Pager ($cgi->offset);
//$pg->url = site_current () . '?table=' . urlencode ($cgi->table) . '&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort);
// ------------------
$pg = new Pager ($cgi->offset);
$pg->url = site_current () . '?table=' . urlencode ($cgi->table) . '&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort);
$pg->getInfo ();
// Check on ? 
$pos = strpos ( $_SERVER['REQUEST_URI'] , "?" );
if($pos === false) {
    $url = $_SERVER['REQUEST_URI'] . "?";
} else {
    $url = $_SERVER['REQUEST_URI'];
}
$pg->setUrl (preg_replace ('/&offset=[0-9]+/', '', $url));
// END: SEMIAS
// build query
$sql = 'select * from ' . $cgi->table;
if ($cgi->orderBy) {
	$sql .= ' order by ' . $cgi->orderBy;
	if ($cgi->sort) {
		$sql .= ' ' . $cgi->sort;
	}
}

// execute

$res = $pg->query ($sql);
if ($res === false) {
	die ($pg->error);
}

$tbl = db_table ($cgi->table);
$tbl->getInfo ();

foreach ($tbl->info as $key) {
	$key = (array) $key;
	$key = array_shift ($key);
	$headers[] = new TableHeader ($key, ucwords (str_replace ('_', ' ', $key)));
}

function myadm_shorten ($value) {
	if (strlen ($value) > 32) {
		$value = htmlentities (substr ($value, 0, 29)) . '...';
	} elseif (empty ($value)) {
		$value = '&nbsp;';
	} else {
		$value = htmlentities ($value);
	}
	return $value;
}

template_simple_register ('pager', $pg);
echo template_simple ('browse.spt', array (
	'headers' => $headers,
	'pkey' => $tbl->pkey,
));

?>