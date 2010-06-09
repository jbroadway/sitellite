<?php

page_title ('SiteShop 2 - ' . intl_get ('Sales'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'sales'));

$s = new Sale ();
$s->orderBy ('start_date desc, until_date desc');
$parameters['list'] = $s->find (array ());

echo template_simple ('admin_sales.spt', $parameters);

?>