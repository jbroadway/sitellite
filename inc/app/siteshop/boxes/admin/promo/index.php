<?php

page_title ('SiteShop 2 - ' . intl_get ('Promo Codes'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'promo'));

$s = new Promo ();
$s->orderBy ('expires desc');
$parameters['list'] = $s->find (array ());

echo template_simple ('admin_promo.spt', $parameters);

?>