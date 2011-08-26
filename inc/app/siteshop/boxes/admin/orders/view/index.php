<?php

page_title ('SiteShop 2 - ' . intl_get ('Orders'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'orders'));

$o = new Order ($parameters['id']);

$ord = $o->makeObj ();

$ord->products = $o->getDetails ();
$ord->history = $o->getHistory ();

if (@file_exists ('inc/app/siteshop/html/admin_orders_print_header.spt')) {
	echo '<div id="print-header">' . template_simple ('admin_orders_print_header.spt', $ord) . '</div>';
}

echo template_simple ('admin_orders_view.spt', $ord);

?>