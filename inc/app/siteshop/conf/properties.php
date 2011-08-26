<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

$conf = ini_parse ('inc/app/siteshop/conf/settings.php');

appconf_set ('page_title', $conf['General']['page_title']);

appconf_set ('paypal_id', $conf['General']['paypal_id']);

appconf_set ('taxes', $conf['Taxes']);

appconf_set ('order_notices', $conf['General']['order_notices']);
appconf_set ('currency_code', $conf['General']['currency_code']);
appconf_set ('shipping_base', $conf['Shipping']['base']);
appconf_set ('shipping_max', $conf['Shipping']['max']);
appconf_set ('shipping_free', $conf['Shipping']['free']);
appconf_set ('checkout_handler', $conf['Display']['checkout_handler']);

appconf_set ('default_thumbnail', $conf['Display']['default_thumbnail']);

page_add_style (site_prefix () . '/' . $conf['Display']['default_style']);

if ($conf['Display']['page_alias']) {
	page_id ($conf['Display']['page_alias']);
}
if ($conf['Display']['below_page']) {
	page_below ($conf['Display']['below_page']);
}
if ($conf['Display']['page_template']) {
	page_template ($conf['Display']['page_template']);
}
if ($conf['Display']['checkout_template'] && 
	(strpos ($_SERVER['REQUEST_URI'], 'siteshop-cart-action') ||
	 strpos ($_SERVER['REQUEST_URI'], 'siteshop-checkout-action') ||
	 strpos ($_SERVER['REQUEST_URI'], 'siteshop-login-action') ||
	 strpos ($_SERVER['REQUEST_URI'], 'siteshop-register-form'))) {

	page_template ($conf['Display']['checkout_template']);
}

appconf_set ('customer_registration_return_email', $conf['Display']['customer_registration_return_email']);

appconf_set ('alternate_index', $conf['Display']['alternate_index']);
appconf_set ('alternate_product', $conf['Display']['alternate_product']);
appconf_set ('product_comments', $conf['Display']['product_comments']);
appconf_set ('product_ratings', $conf['Display']['product_ratings']);

appconf_set ('availability', array (
	1 => intl_get ('Usually Ships Within 24 Hours'),
	2 => intl_get ('Usually Ships in 1-2 Business Days'),
	3 => intl_get ('Usually Ships in 2-3 Days'),
	4 => intl_get ('Usually Ships in 1-2 Weeks'),
	5 => intl_get ('Usually Ships in 2-3 Weeks'),
	6 => intl_get ('Back-Ordered'),
	7 => intl_get ('Temporarily Unavailable'),
	8 => intl_get ('Unavailable'),
));

appconf_set ('weight', array (
	0 => intl_get ('Normal'),
	1 => intl_get ('Special'),
	2 => intl_get ('On Sale'),
	3 => intl_get ('Hot Seller'),
	4 => intl_get ('Featured Product'),
));

loader_import ('siteshop.Objects');
loader_import ('siteshop.Functions');
loader_import ('siteshop.Cart');

?>