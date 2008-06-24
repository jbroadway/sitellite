<?php

loader_import ('saf.Misc.RPC');

global $cgi;

if (! $cgi->page) {
	echo rpc_response (false);
	exit;
}

if (! $cgi->rev) {
	echo rpc_response (false);
	exit;
}

$level = 0;
if (session_valid ()) {
	$level++;
}
if (session_admin ()) {
	$level++;
}

$current = db_single (
	'select * from sitewiki_page where id = ?',
	$cgi->page
);

if (! $current) {
	echo rpc_response (false);
	exit;
}

$revision = db_single (
	'select * from sitewiki_page_sv where id = ? and sv_autoid = ?',
	$cgi->page,
	$cgi->rev
);

if (! $revision) {
	echo rpc_response (false);
	exit;
}

if ($current->view_level > $level && $current->owner != session_username ()) {
	echo rpc_response (false);
	exit;
}

if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/sitewiki/lib/Ext' . $join . ini_get ('include_path'));

loader_import ('sitewiki.Ext.Text.Diff');
loader_import ('sitewiki.Ext.Text.Diff.Renderer');
loader_import ('sitewiki.Ext.Text.Diff.Renderer.inline');

$diff = new Text_Diff (
	explode ("\n", $revision->body),
	explode ("\n", $current->body)
);

$renderer = new Text_Diff_Renderer_inline ();

$out = $renderer->render ($diff);

echo rpc_response ($out);
exit;

?>