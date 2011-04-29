<?php

echo template_simple (
	'list.spt',
	array (
		'list' => db_fetch_array ('select * from sitequotes_entry where id = ?', $parameters['id']),
	)
);

?>