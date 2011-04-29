<?php

global $cgi;

loader_import ('saf.Misc.RPC');

if (! $cgi->items || ! $cgi->path) {
	echo rpc_response (false);
	exit;
}

$items = preg_split ('/, ?/', $cgi->items);

loader_import ('saf.File.Directory');

foreach ($items as $item) {
	if (! Dir::build ($cgi->path . '/' . $item, 0774)) {
		echo rpc_response (false);
		exit;
	}
}

echo rpc_response (true);
exit;

?>