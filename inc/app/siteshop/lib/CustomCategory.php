<?php

loader_import ('saf.Database.Generic');
loader_import ('siteshop.Functions');

class CustomCategory extends Generic {
	function getSortedProducts ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		if (session_admin ()) {
			$sql = session_allowed_sql ();
		} else {
			$sql = session_approved_sql ();
		}

		$list = db_fetch_array (
			'select b.* from siteshop_category a, siteshop_product_category j, siteshop_product b
			where a.id = ? and a.id = j.category_id and b.id = j.product_id and b.availability != 8 and
			(b.quantity = -1 or b.quantity > 0) and ' . $sql . ' order by b.weight desc',
			$id
		);

		foreach (array_keys ($list) as $k) {
			$p = new Product ($list[$k]->id);
			$price = $p->getPrice ();
			if ($price != $p->val ('price')) {
				$list[$k]->sale = true;
				$list[$k]->sale_price = $price;
			}
		}

		return $list;
	}

	function listAssoc () {
		$c = new Category ();
		$c->orderBy ('name asc');
		$list = $c->find (array ());
		$out = array ('' => '- SELECT -');
		if (empty ($list)) {
			return $out;
		}
		foreach ($list as $item) {
			$out[$item->id] = $item->name;
		}
		return $out;
	}
}

?>