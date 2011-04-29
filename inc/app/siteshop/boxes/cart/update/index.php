<?php

foreach ($parameters['qty'] as $id => $qty) {
	Cart::qty ($id, $qty);
}

if (isset ($parameters['promo'])) {
	$promo = Promo::code ($parameters['promo']);
	if ($promo) {
		Cart::addPromo ($promo);
	}
}

if (isset ($parameters['checkout'])) {
	header ('Location: ' . site_prefix () . '/index/siteshop-checkout-action');
	exit;
}

header ('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>