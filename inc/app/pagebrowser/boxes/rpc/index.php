<?php

loader_import ('saf.Misc.RPC');
loader_import ('pagebrowser.PageBrowser');

echo rpc_handle (new PageBrowser (), $parameters);
exit;

?>