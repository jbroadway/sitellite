<?php

class Cart {
	function Cart () {
		$this->init ();
	}

	function init () {
		if (! is_array ($_SESSION['siteshop_cart'])) {
			$_SESSION['siteshop_cart'] = array ();
		}
	}

	function addPromo ($p) {
		if (Cart::hasPromo ()) {
			// only allow one
			return false;
		}
		$_SESSION['siteshop_promo'] = $p->code;
	}

	function hasPromo () {
		if (isset ($_SESSION['siteshop_promo'])) {
			return true;
		}
		return false;
	}

	function add ($id, $price = false, $options) {
		if (! $price) {
			$price = Product::getPrice ($id);
		}

		if (empty ($options)) {
			$_SESSION['siteshop_cart'][$id . '_'] = array (1, $price, $options, $id);
		} else {
			$_SESSION['siteshop_cart'][$id . '_' . implode ('_', $options)] = array (1, $price, $options, $id);
		}
	}

	function changePrice ($id, $price = false) {
		if (! $price) {
			$price = Product::getPrice ($id);
		}

		foreach ($_SESSION['siteshop_cart'] as $k => $v) {
			if (strpos($k, $id.'_') === 0) {
				$_SESSION['siteshop_cart'][$k][1] = $price;
			}
		}

		if (empty ($options)) {
			$_SESSION['siteshop_cart'][$id . '_'] = array (1, $price, $options, $id);
		} else {
			$_SESSION['siteshop_cart'][$id . '_' . implode ('_',$options)] = array (1, $price, $options, $id);
		}
	}

	function remove ($id) {
		unset ($_SESSION['siteshop_cart'][$id]);
	}

	function qty ($id, $qty) {
		$_SESSION['siteshop_cart'][$id][0] = $qty;
	}

	function view () {
		$cart = array ();
		foreach ($_SESSION['siteshop_cart'] as $k => $info) {
			$qty = $info[0];
			$price = $info[1];
			$options = $info[2];
			$id = $info[3];
			$p = new Product ($id);

			$index = $id.'_';
			if (! empty ($options)) {
				$index = $id . '_' . implode ('_', $options);
			}

			$cart[$index] = $p->makeObj ();
			$cart[$index]->qty = $qty;
			$cart[$index]->price = $price;
			$cart[$index]->options = array ();
			$cart[$index]->id = $id;
			$cart[$index]->options_str = '';
			if (! empty ($options)) {
				foreach ($options as $o) {
					$to = new Option ($o);
					$cart[$index]->options[$to->val ('id')] = array (
						'id' => $to->val('id'), 
						'name' => $to->val('name'), 
						'type' => $to->val('type'),
					);
					$cart[$index]->option_str .= ucwords ($to->val ('type')) . ': ' . ucwords ($to->val ('name')) . ', ';
				}
				$cart[$index]->options_str = substr ($cart[$index]->option_str, 0, -2);
			}
		}
		return $cart;
	}

	function items () {
		return count ($_SESSION['siteshop_cart']);
	}

	function subtotal ($id = false) {
		Cart::init ();

		if ($id) {
			return money_format ('%^!n', $_SESSION['siteshop_cart'][$id][0] * $_SESSION['siteshop_cart'][$id][1]);
		}
		$t = 0;
		foreach ($_SESSION['siteshop_cart'] as $id => $info) {
			$qty = $info[0];
			$price = $info[1];
			$t += $price * $qty;
		}
		return money_format ('%^!n', $t);
	}

	function shipping () {
		$t = 0;
		$sub = 0;
		foreach ($_SESSION['siteshop_cart'] as $id => $info) {
			$qty = $info[0];
			$price = $info[1];
			$p = new Product ($id);
			$sub += $price * $qty;
			$ship = $p->val ('shipping');
			if ($ship == 0) {
				$ship = appconf ('shipping_base');
			}
			$t += $ship * $qty;
		}
		if (appconf ('shipping_max') && $t > appconf ('shipping_max')) {
			$t = appconf ('shipping_max');
		}
		if (appconf ('shipping_free') && $sub > appconf ('shipping_free')) {
			$t = 0;
		}
		return money_format ('%^!n', $t);
	}

	function tax () {
		$sub = Cart::shipping ();
		foreach ($_SESSION['siteshop_cart'] as $id => $info) {
			if (Product::taxable ($id)) {
				$qty = $info[0];
				$price = $info[1];
				$sub += $price * $qty;
			}
		}
		$t = 0;
		foreach (appconf ('taxes') as $tax => $amt) {
			$t += $sub * $amt;
		}
		return money_format ('%^!n', $t);
	}

	function promoCode () {
		if (Cart::hasPromo ()) {
			return $_SESSION['siteshop_promo'];
		}
		return '';
	}

	function promoName () {
		if (Cart::hasPromo ()) {
			$promo = Promo::code ($_SESSION['siteshop_promo']);
			if ($promo->discount_type == 'dollars') {
				return '$' . money_format ('%^!n', $promo->discount) . ' off';
			} else {
				return round ($promo->discount) . '% off';
			}
		}
		return false;
	}

	function promo () {
		if (Cart::hasPromo ()) {
			$promo = Promo::code ($_SESSION['siteshop_promo']);
			if ($promo->discount_type == 'dollars') {
				return '-' . money_format ('%^!n', $promo->discount);
			} else {
				return '-' . money_format ('%^!n', ($promo->discount * Cart::subtotal ()) / 100);
			}
		}
		return 0;
	}

	function pptotal () {
		return money_format ('%^!n', Cart::subtotal () + Cart::promo ());
	}

	function total () {
		return money_format ('%^!n', Cart::subtotal () + Cart::shipping () + Cart::tax () + Cart::promo ());
	}

	function clear () {
		$_SESSION['siteshop_cart'] = array ();
	}
}

?>