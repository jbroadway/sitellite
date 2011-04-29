<?php

loader_import ('saf.Misc.RPC');
loader_import ('wffolderbrowser.PageBrowser');

echo rpc_handle (new PageBrowser (), $parameters);
exit;

?>