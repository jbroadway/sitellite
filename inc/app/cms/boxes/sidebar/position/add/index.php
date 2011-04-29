<?php

$new = explode (',', $parameters['name']);

foreach ($new as $word) {
	$word = trim ($word);
	db_execute ('insert into sitellite_sidebar_position (id) values (?)', $word);
}

echo '<html><body onload="window.close ()"></body></html>';
exit;

?>