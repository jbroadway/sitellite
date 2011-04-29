<?php

loader_import ('saf.Database.Generic');

class CustomSale extends Generic {
	function loadCurrent () {
		$c = db_single (
			'select * from siteshop_sale where start_date <= ? and until_date >= ? limit 1',
			date ('Y-m-d H:i:s'),
			date ('Y-m-d H:i:s')
		);
		if (is_object ($c)) {
			$this->setCurrent ($c);
			return true;
		}
		return false;
	}

	function top ($n) {
		if (session_admin ()) {
			$sql = session_allowed_sql ();
		} else {
			$sql = session_approved_sql ();
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_sale a, siteshop_sale_product j, siteshop_product b
			where a.id = ? and a.id = j.sale_id and b.id = j.product_id and b.availability != 8 and
			(b.quantity = -1 or b.quantity > 0) and ' . $sql . ' order by b.weight desc
			limit ' . $n,
			$this->val ('id')
		);
	}

	function all () {
		if (session_admin ()) {
			$sql = session_allowed_sql ();
		} else {
			$sql = session_approved_sql ();
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_sale a, siteshop_sale_product j, siteshop_product b
			where a.id = ? and a.id = j.sale_id and b.id = j.product_id and b.availability != 8 and
			(b.quantity = -1 or b.quantity > 0) and ' . $sql . ' order by b.weight desc',
			$this->val ('id')
		);
	}
}

?>