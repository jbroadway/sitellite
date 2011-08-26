<?php

loader_import ('saf.Misc.RPC');
loader_import ('sitetag.TagCloud');

if (! isset ($parameters['set'])) {
	$parameters['set'] = 'keywords';
}

echo rpc_handle (new TagCloud ($parameters['set']), $parameters);
exit;

?>
