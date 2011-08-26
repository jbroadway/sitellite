<?php

echo template_simple (
	'quote.spt',
	array (
		'result' => db_fetch_array ('select * from sitequotes_entry where id = ?', $parameters['id']),
	)
);

?>