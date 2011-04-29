<?php

page_title ('SiteShop 2 - ' . intl_get ('Checkout Offers'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'offers'));

$s = new CheckoutOffer ();
$s->orderBy ('offer_number asc');
$parameters['list'] = $s->find (array ());

echo template_simple ('admin_offers.spt', $parameters);

?>