<?php

header ('Content-Type: text/xml');

echo template_simple (
	'opensearch.spt',
	array (
		'searchTerms' => '{searchTerms}',
		'startPage' => '{startPage}',
	)
);

exit;

?>