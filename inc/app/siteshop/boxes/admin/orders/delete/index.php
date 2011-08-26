<?php

$o = new Order ();

foreach ($parameters['_key'] as $id) {
	$o->remove ($id);
}

header ('Location: ' . site_prefix () . '/index/siteshop-admin-orders-action');
exit;

?>