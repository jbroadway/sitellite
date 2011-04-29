<?php

$code = db_shift (
	'select c.code from siteinvoice_client c, siteinvoice_invoice i
	where i.client_id = c.id and i.id = ?',
	$parameters['id']
);

header ('Content-Type: application/pdf');
header ('Content-Disposition: inline; filename=' . strtolower ($code) . '-' . $parameters['id'] . '.pdf');
echo join ('', file ('inc/app/siteinvoice/data/' . $parameters['id'] . '.pdf'));
exit;

?>