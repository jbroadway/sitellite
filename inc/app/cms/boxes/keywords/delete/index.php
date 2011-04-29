<?php

$old = explode (',', $parameters['keyword']);

foreach ($old as $word) {
	$word = trim ($word);
	db_execute ('delete from sitellite_keyword where word = ?', $word);
}

//echo '<html><body onload="window.close ()"></body></html>';

//loader_import ('saf.Misc.RPC');

//rpc_response (true);

header ('Location: ' . site_prefix () . '/index/cms-keywords-action?el=' . $parameters['el'] . '&sel=' . $parameters['sel']);
exit;

?>