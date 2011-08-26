<?php

$old = explode (',', $parameters['name']);

foreach ($old as $word) {
	$word = trim ($word);
	db_execute ('delete from sitellite_sidebar_position where id = ?', $word);
}

echo '<html><body onload="window.close ()"></body></html>';
exit;

?>