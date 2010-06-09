<?php

$s = new Sale ();

$s->remove ($parameters['_key']);

header ('Location: ' . site_prefix () . '/index/siteshop-admin-sales-action');
exit;

?>