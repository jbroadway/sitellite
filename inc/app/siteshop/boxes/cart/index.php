<?php

page_title (intl_get ('View Cart'));

echo template_simple ('cart.spt', Cart::view ());

?>