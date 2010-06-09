<?php

switch ($parameters['step']) {
	case 2:
		if (! session_valid() && 
			isset ($parameters['password_new_customer']) && 
			! empty ($parameters['password_new_customer'])) {
	
			// 1. insert into sitellite_user
			$res = session_user_add (
				array (
					'username' => $parameters['email'],
					'password' => better_crypt ($parameters['password_new_customer']),
					'firstname' => $parameters ['bill_to'],
					'country' => $parameters['bill_country'],
					'province' => $parameters['bill_state'],
					'address1' => $parameters['bill_address'],
					'address2' => $parameters['bill_address2'],
					'phone' => $parameters['phone'],
					'city' => $parameters['bill_city'],
					'email' => $parameters['email'],
					'postal_code' => $parameters['bill_zip'],
					'session_id' => null,
					'role' => 'member',
					'team' => 'none',
					'public' => 'no',
					'registered' => date ('Y-m-d H:i:s'),
					'modified' => date ('Y-m-d H:i:s'),
				)
			);
			
			if (empty ($res)) {
				
				$parameters['new_user_error'] = '1';
				
				page_title (intl_get ('Customer Information'));
				echo template_simple ('checkout1_registered.spt', $parameters);
				return;
			}
			
			// 2. email confirmation
			@mail ($parameters['email'], 'Customer Confirmation', template_simple ('register_confirmation.spt', $parameters), 'From: ' . appconf ('customer_registration_return_email'));

			global $session;
			$session->username = $parameters['email'];
			$session->password = $parameters['password_new_customer'];
			$session->start ();
			
		}
		
		// 1. handle customer information
		$info = $parameters;
		unset ($info['password_new_customer']);
		unset ($info['password_new_customer_verify']);
		unset ($info['_rewrite_sticky']);
		unset ($info['param']);
		unset ($info['files']);
		unset ($info['error']);
		unset ($info['step']);
		unset ($info['page']);
		unset ($info['mode']);

		if ($info['ship_same'] == 'yes') {
			$info['ship_to'] = $info['bill_to'];
			$info['ship_address'] = $info['bill_address'];
			$info['ship_address2'] = $info['bill_address2'];
			$info['ship_city'] = $info['bill_city'];
			$info['ship_state'] = $info['bill_state'];
			$info['ship_country'] = $info['bill_country'];
			$info['ship_zip'] = $info['bill_zip'];
		}
		unset ($info['ship_same']);
		$_SESSION['siteshop_customer_info'] = $info;

		// 2. show checkout offers
		$tcart_prods = array_keys (Cart::view ());
		$cart_prods = array ();
		foreach ($tcart_prods as $prod) {
			$tarr = explode ('_', $prod);
			$cart_prods[] = $tarr[0];
		}

		$o = new CheckoutOffer ();
		$o->orderBy ('offer_number asc');
		$list = $o->find (array ('product_id not in(' . join (',', $cart_prods) . ')'));

		if (count ($list) > 0) {
			foreach (array_keys ($list) as $k) {
				$p = new Product ($list[$k]->product_id);
				$list[$k]->price = $p->val ('price');
			}
			page_title (intl_get ('Special Offers'));
			echo template_simple ('checkout2.spt', $list);
		} else {
			header ('Location: ' . site_prefix () . '/index/siteshop-checkout-action?step=3');
			exit;
		}
		break;
	case 3:
		// 1. handle checkout offers
		if (is_array ($parameters['add'])) {
			foreach ($parameters['add'] as $id) {
				// should this be add?
				Cart::changePrice ($id, db_shift ('select sale_price from siteshop_checkout_offer where product_id = ?', $id));
			}
		}

		// 2. redirect to paypal
		page_title (intl_get ('Verify Your Order'));
		$info = $_SESSION['siteshop_customer_info'];
		$info['cart'] = Cart::view ();
		echo template_simple ('checkout3.spt', $info);
		break;
	case 'success':
		$username = '';
		if (session_valid ()) {
			$username = session_username ();
		}
		// 1. save order to database
		$info = $_SESSION['siteshop_customer_info'];
		$info['user_id'] = $username;
		$info['status'] = 'new';
		$info['ts'] = date ('Y-m-d H:i:s');
		$info['subtotal'] = Cart::subtotal ();
		$info['shipping'] = Cart::shipping ();
		$info['taxes'] = Cart::tax ();
		$info['promo_code'] = Cart::promoCode ();
		$info['promo_discount'] = Cart::promo ();
		$info['total'] = Cart::total ();
		$info['tracking'] = '';

		$o = new Order ($info);

		$info['id'] = $o->val ('id');
		$info['cart'] = Cart::view ();

		foreach ($info['cart'] as $k => $item) {
			$item->options = serialize ($item->options);
			$o->addProduct ($item);
			Product::updateQuantity ($k, $item->qty);
		}

		$o->recordStatus ();

		Cart::clear ();

		// 2. email receipt
		mail (
			$info['email'],
			intl_get ('Order Receipt'),
			template_simple ('order_receipt_email.spt', $info),
			'From: ' . appconf ('order_notices')
		);

		// 3. email notice
		mail (
			appconf ('order_notices'),
			intl_get ('Order Notification'),
			template_simple ('order_notice_email.spt', $info),
			'From: ' . appconf ('order_notices')
		);

		page_title (intl_get ('Thank You'));
		echo template_simple ('checkout4.spt');
		break;
	case 'cancelled':
		// cancellation
		page_title (intl_get ('Order Cancelled'));
		echo template_simple ('checkout5.spt');
		break;
	default:
		$user = session_get_user ();
		
		$data = array (
			'bill_to' => $user->firstname,
			'bill_country' => $user->country,
			'bill_state' => $user->province,
			'bill_address' => $user->address1,
			'bill_address2' => $user->address2,
			'phone' => $user->phone,
			'bill_city' => $user->city,
			'email' => $user->email,
			'bill_zip' => $user->postal_code
		);
		//info ($user);
		page_title (intl_get ('Customer Information'));
		echo template_simple ('checkout1_registered.spt', $data);
		break;
}

?>