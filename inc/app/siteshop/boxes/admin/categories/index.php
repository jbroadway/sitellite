<?php

loader_import ('saf.GUI.Prompt');

page_title ('SiteShop 2 - ' . intl_get ('Categories'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'categories'));

$s = new Category ();
$s->orderBy ('weight desc, name asc');
$parameters['list'] = $s->find (array ());

echo template_simple ('admin_categories.spt', $parameters);

?>