<?php

$res = db_fetch_array (
		'select * from sitequotes_entry order by RAND() limit 1'
		);

echo template_simple (
	'quote.spt',
	array (
		'result' => $res
	)
);

?>