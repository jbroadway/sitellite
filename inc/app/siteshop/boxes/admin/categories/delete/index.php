<?php

$s = new Category ();

$s->remove ($parameters['_key']);

header ('Location: ' . site_prefix () . '/index/siteshop-admin-categories-action');
exit;

?>