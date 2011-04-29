<?php

loader_import ('saf.Misc.RPC');
loader_import ('cms.Versioning.Parallel');

class Parallel_RPC_Handler {
	function click ($page_id, $revision_id) {
		$p = new Parallel ((object) array ('id' => $page_id));
		return $p->clicked ($revision_id);
	}
}

echo rpc_handle (new Parallel_RPC_Handler (), $parameters);
exit;

?>