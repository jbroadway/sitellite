<?php

loader_import ('siteshop.CustomProduct');

class Product extends CustomProduct {
	function Product ($id = false) {
		parent::CustomProduct ('siteshop_product', 'id');
		$this->usePermissions = true;
		$this->multilingual = true;

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}


		$this->_cascade[] = array ('siteshop_product_option', 'product_id');
		$this->_cascade[] = array ('siteshop_product_category', 'product_id');
	}

	function &setOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_product_option (product_id, option_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_product_option where product_id = ? and option_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getOptions ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_product a, siteshop_product_option j, siteshop_option b
			where a.id = ? and a.id = j.product_id and b.id = j.option_id',
			$id
		);
	}

	function &setCategory (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_product_category (product_id, category_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetCategory (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_product_category where product_id = ? and category_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getCategories ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_product a, siteshop_product_category j, siteshop_category b
			where a.id = ? and a.id = j.product_id and b.id = j.category_id',
			$id
		);
	}

	function &setOrder (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_order_product (product_id, order_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetOrder (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_order_product where product_id = ? and order_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getOrders ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_product a, siteshop_order_product j, siteshop_order b
			where a.id = ? and a.id = j.product_id and b.id = j.order_id',
			$id
		);
	}

	function &setSale (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_sale_product (product_id, sale_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetSale (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_sale_product where product_id = ? and sale_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getSales ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_product a, siteshop_sale_product j, siteshop_sale b
			where a.id = ? and a.id = j.product_id and b.id = j.sale_id',
			$id
		);
	}
}

loader_import ('siteshop.CustomCategory');

class Category extends CustomCategory {
	function Category ($id = false) {
		parent::CustomCategory ('siteshop_category', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// Category cascade
	}

	function &setProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_product_category (category_id, product_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_product_category where category_id = ? and product_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getProducts ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_category a, siteshop_product_category j, siteshop_product b
			where a.id = ? and a.id = j.category_id and b.id = j.product_id',
			$id
		);
	}
}

loader_import ('siteshop.CustomOrder');

class Order extends CustomOrder {
	function Order ($id = false) {
		parent::CustomOrder ('siteshop_order', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}


		$this->_cascade[] = array ('siteshop_order_product', 'order_id');
	}

	function &setProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_order_product (order_id, product_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_order_product where order_id = ? and product_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getProducts ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_order a, siteshop_order_product j, siteshop_product b
			where a.id = ? and a.id = j.order_id and b.id = j.product_id',
			$id
		);
	}
}

loader_import ('siteshop.CustomOption');

class Option extends CustomOption {
	function Option ($id = false) {
		parent::CustomOption ('siteshop_option', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}


		$this->_cascade['ProductOption'] = 'option_id';
	}

	function &setProductOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
			$o->_current->option_id = $this->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update siteshop_product_option set option_id = ? where id = ?',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetProductOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
			$o->_current->option_id = 0;
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update siteshop_product_option set option_id = ? where id = ?',
			0,
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getProductOptions ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select * from siteshop_product_option
			where option_id = ?',
			$id
		);
	}

	function &setProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_product_option (option_id, product_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_product_option where option_id = ? and product_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getProducts ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_option a, siteshop_product_option j, siteshop_product b
			where a.id = ? and a.id = j.option_id and b.id = j.product_id',
			$id
		);
	}
}

class ProductOption extends Generic {
	function ProductOption ($id = false) {
		parent::Generic ('siteshop_product_option', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// ProductOption cascade
	}

	function &setOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update siteshop_product_option set option_id = ? where id = ?',
			$k,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->option_id = $k;
		return $o;
	}

	function unsetOption (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update siteshop_product_option set option_id = ? where id = ?',
			0,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->option_id = 0;
		return true;
	}

	function getOption () {
		return db_single (
			'select * from siteshop_option
			where id = ?',
			$this->val ('option_id')
		);
	}
}

class OptionType extends Generic {
	function OptionType ($id = false) {
		parent::Generic ('siteshop_option_type', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// OptionType cascade
	}
}

loader_import ('siteshop.CustomSale');

class Sale extends CustomSale {
	function Sale ($id = false) {
		parent::CustomSale ('siteshop_sale', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}


		$this->_cascade[] = array ('siteshop_sale_product', 'sale_id');
	}

	function &setProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'insert into siteshop_sale_product (sale_id, product_id) values (?, ?)',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetProduct (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'delete from siteshop_sale_product where sale_id = ? and product_id = ?',
			$this->val ('id'),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getProducts ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select b.*, j.* from siteshop_sale a, siteshop_sale_product j, siteshop_product b
			where a.id = ? and a.id = j.sale_id and b.id = j.product_id',
			$id
		);
	}
}

loader_import ('siteshop.CustomPromo');

class Promo extends CustomPromo {
	function Promo ($id = false) {
		parent::CustomPromo ('siteshop_promo_code', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// Promo cascade
	}
}

class CheckoutOffer extends Generic {
	function CheckoutOffer ($id = false) {
		parent::Generic ('siteshop_checkout_offer', 'id');
		
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// CheckoutOffer cascade
	}
}

?>