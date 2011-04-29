<?php

page_title ('SiteShop 2 - ' . intl_get ('Product Options'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'products'));
loader_import('siteshop.Objects');

$o = new Option;
$o->orderBy ('type asc, name asc');

$parameters['list'] = $o->find (array());

echo template_simple ('admin_options.spt', $parameters);

?>