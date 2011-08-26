<?php

$data = array (
	'items' => Cart::items (),
	'total' => Cart::subtotal (),
);

echo template_simple ('cart_sidebar.spt', $data);

?>