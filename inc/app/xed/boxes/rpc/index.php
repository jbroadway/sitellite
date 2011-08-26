<?php

loader_import ('saf.Misc.RPC');

class Xed_RPC {
	function cleaners ($ifname, $data) {
		loader_import ('xed.Cleaners');
		return array (
			$ifname,
			the_cleaners ($data)
		);
	}
}

echo rpc_handle (new Xed_RPC (), $parameters);
exit;

?>