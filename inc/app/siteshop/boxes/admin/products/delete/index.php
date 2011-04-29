<?php

$p = new Product ();

foreach ($parameters['_key'] as $id) {
	$p->remove ($id);
}

header ('Location: ' . site_prefix () . '/index/siteshop-admin-products-action');
exit;

?>