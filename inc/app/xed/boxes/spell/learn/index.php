<?php

loader_import ('saf.Misc.RPC');
loader_import ('xed.Xspel');

if (empty ($parameters['lang'])) {
	$parameters['lang'] = appconf ('pspell_default_language');
}

$xs = new Xspel ($parameters['lang']);

$xs->addPersonal ($parameters['word']);

echo rpc_response (true);

exit;

?>