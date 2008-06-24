<?php

loader_import ('saf.Misc.RPC');
loader_import ('sitetemplate.Functions');

class SiteTemplate_RPC_Handler {
	function getBoxes ($app) {
		return sitetemplate_get_boxes ($app);
	}
}

echo rpc_handle (new SiteTemplate_RPC_Handler (), $parameters);

exit;

?>