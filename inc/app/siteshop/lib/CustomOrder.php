<?php

loader_import ('saf.Database.Generic');

class CustomOrder extends Generic {
	function overview () {
		$out = array ();
		$orders = db_pairs (
			'select status, count(*) from siteshop_order group by status'
		);
		$out['orders_new'] = $orders['new'];
		$out['orders_partly_shipped'] = $orders['partly-shipped'];
		$out['orders_shipped'] = $orders['shipped'];
		$out['orders_cancelled'] = $orders['cancelled'];
		$out['orders_total'] = array_sum ($orders);

		$out['sales_today'] = money_format ('%!n', db_shift (
			'select sum(total) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-m-d 00:00:00'),
			date ('Y-m-d 23:59:59')
		));

		$out['sales_month'] = money_format ('%!n', db_shift (
			'select sum(total) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-m-01 00:00:00'),
			date ('Y-m-t 23:59:59')
		));

		$out['sales_year'] = money_format ('%!n', db_shift (
			'select sum(total) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-01-01 00:00:00'),
			date ('Y-12-31 23:59:59')
		));

		$out['orders_today'] = db_shift (
			'select count(*) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-m-d 00:00:00'),
			date ('Y-m-d 23:59:59')
		);

		$out['orders_month'] = db_shift (
			'select count(*) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-m-01 00:00:00'),
			date ('Y-m-t 23:59:59')
		);

		$out['orders_year'] = db_shift (
			'select count(*) from siteshop_order where ts >= ? and ts <= ?',
			date ('Y-01-01 00:00:00'),
			date ('Y-12-31 23:59:59')
		);

		$out['products'] = db_shift ('select count(*) from siteshop_product');
		$out['offers'] = db_shift ('select count(*) from siteshop_checkout_offer');
		$out['sales'] = db_shift ('select count(*) from siteshop_sale');
		$out['categories'] = db_shift ('select count(*) from siteshop_category');

		return $out;
	}

	function addProduct ($p) {
		$prod = new Product ($p->id);
		$p->sku = $prod->val ('sku');
		$p->name = $prod->val ('name');
		return db_execute (
			'insert into siteshop_order_product
			(order_id, product_id, product_sku, product_name, price, shipping, quantity, product_options)
			values (?, ?, ?, ?, ?, ?, ?, ?)',
			$this->val ('id'),
			$p->id,
			$p->sku,
			$p->name,
			$p->price,
			$p->shipping,
			$p->qty,
			$p->options
		);
	}

	function recordStatus () {
		return db_execute (
			'insert into siteshop_order_status values (?, now(), ?)',
			$this->val ('id'),
			$this->val ('status')
		);
	}

	function getHistory () {
		return db_fetch_array (
			'select * from siteshop_order_status where order_id = ? order by ts asc',
			$this->val ('id')
		);
	}

	function getDetails ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		$res = db_fetch_array (
			'select order_id, product_id, product_sku as sku, product_name as name, price, shipping, quantity, product_options from siteshop_order_product
			where order_id = ?',
			$id
		);
		foreach ($res as $k => $v) {
			if (empty ($v->sku) || empty ($v->name)) {
				$p = new Product ($v->product_id);
				$res[$k]->sku = $p->val ('sku');
				$res[$k]->name = $p->val ('name');
			}
			$res[$k]->options = unserialize ($v->product_options);
		}
		return $res;
	}
}

?>