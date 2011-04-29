--TEST--
saf.Cart
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Cart');

// constructor method

$item = new Item;

// constructor method

$cart = new Cart ('$customer_id');

// total_items() method

var_dump ($cart->total_items ('$tpl'));

// subtotal() method

var_dump ($cart->subtotal ('$tpl'));

// add_tax() method

var_dump ($cart->add_tax ('$tpl'));

// view_button() method

var_dump ($cart->view_button ('$tpl'));

// checkout_button() method

var_dump ($cart->checkout_button ('$tpl'));

// add_item() method

var_dump ($cart->add_item ('$sku', '$item'));

// store() method

var_dump ($cart->store ());

// retrieve() method

var_dump ($cart->retrieve ());

// checkout() method

var_dump ($cart->checkout ());

// save() method

var_dump ($cart->save ());

// valid() method

var_dump ($cart->valid ());

// error() method

var_dump ($cart->error ());

?>
--EXPECT--
