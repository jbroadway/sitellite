<?php

$s = new CheckoutOffer ();

$s->remove ($parameters['_key']);

header ('Location: ' . site_prefix () . '/index/siteshop-admin-offers-action');
exit;

?>