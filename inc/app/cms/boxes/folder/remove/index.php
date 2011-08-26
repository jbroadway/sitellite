<?php

global $cgi;

loader_import ('saf.Misc.RPC');

if (! $cgi->items || ! $cgi->path) {
	echo rpc_response (false);
	exit;
}

$items = preg_split ('/, ?/', $cgi->items);

foreach ($items as $item) {
	if (empty ($item)) {
		continue;
	}
	if (! rmdir ($cgi->path . '/' . $item)) {
		echo rpc_response (false);
		exit;
	}
}

echo rpc_response (true);
exit;

?>