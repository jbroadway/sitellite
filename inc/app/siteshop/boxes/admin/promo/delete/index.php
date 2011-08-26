<?php

$s = new Promo ();

$s->remove ($parameters['_key']);

header ('Location: ' . site_prefix () . '/index/siteshop-admin-promo-action');
exit;

?>