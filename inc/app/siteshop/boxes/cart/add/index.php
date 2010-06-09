<?php

$options = array();

foreach ($parameters as $k=>$p) {
	if (strpos ($k, 'optiontype_') === 0) {
		$options[] = $p;
	}
}

Cart::add ($parameters['pid'], false, $options);

header ('Location: ' . site_prefix () . '/index/siteshop-cart-action');
exit;

?>