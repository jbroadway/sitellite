<?php

$new = explode (',', $parameters['keyword']);

foreach ($new as $word) {
	$word = trim ($word);
	db_execute ('insert into sitellite_keyword (word) values (?)', $word);
}

//echo '<html><body onload="window.close ()"></body></html>';

//loader_import ('saf.Misc.RPC');

//rpc_response (true);

header ('Location: ' . site_prefix () . '/index/cms-keywords-action?el=' . $parameters['el'] . '&sel=' . $parameters['sel']);
exit;

?>