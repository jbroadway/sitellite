<?php

loader_import ('saf.Database.Generic');

class CustomProduct extends Generic {
	function getThumbnail ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		}
		if (@file_exists ('inc/app/siteshop/data/' . $id . '-1.jpg')) {
			return site_prefix () . '/inc/app/siteshop/data/' . $id . '-1.jpg';
		}
		return site_prefix () . '/' . appconf ('default_thumbnail');
	}

	function getImages ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		}
		$images = array ();
		$i = 1;
		while (@file_exists ('inc/app/siteshop/data/' . $id . '-' . $i . '.jpg')) {
			$images[] = site_prefix () . '/inc/app/siteshop/data/' . $id . '-' . $i . '.jpg';
			$i++;
		}
		if (count ($images) == 0) {
			$images[] = site_prefix () . '/' . appconf ('default_thumbnail');
		}
		return $images;
	}

	function selectAll () {
		return db_pairs (
			'select id, concat(name, \' ($\', price, \')\') from siteshop_product order by name asc'
		);
	}

	function featured ($n) {
		if (session_admin ()) {
			$sql = session_allowed_sql ();
		} else {
			$sql = session_approved_sql ();
		}

		return db_fetch_array (
			'select * from siteshop_product
			where availability != 8 and (quantity = -1 or quantity > 0)
			and ' . $sql . ' order by weight desc
			limit ' . $n
		);
	}

	function getPrice ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		}

		$p = new Product ($id);

		$price = $p->val ('price');

		// check for sale
		$s = new Sale ();
		if ($s->loadCurrent ()) {
			$sale_price = db_shift (
				'select sale_price from siteshop_sale_product where sale_id = ? and product_id = ?',
				$s->val ('id'),
				$id
			);
			if ($sale_price) {
				$price = $sale_price;
			}
		}

		return $price;
	}

	function updateQuantity ($id = false, $qty = 1) {
		if (! $id) {
			$id = $this->val ('id');
		}

		$p = new Product ($id);
		if ($p->val ('quantity') == -1) {
			return true;
		} elseif ($p->val ('quantity') == 0) {
			return false;
		}
		$p->set ('quantity', $p->val ('quantity') - $qty);
		$p->save ();
		return true;
	}

	function taxable ($id = false) {
		if ($id) {
			$p = new Product ($id);
			if ($p->val ('taxable') == 'yes') {
				return true;
			}
			return false;
		}
		if ($this->val ('taxable') == 'yes') {
			return true;
		}
		return false;
	}

	function getAllOptions () {
		$options = db_fetch_array ('
			select distinct o.id, o.name, po.available, o.type from
				(select * from siteshop_option) as o
			left outer join
				(select * from siteshop_product_option where product_id = ?) as po
			on
				o.id = po.option_id order by o.type asc, o.weight, o.name asc',
			$this->val('id')
		);

		return $options;
	}

	function setOptionC ($option_id, $available = 'no') {
		if (empty ($option_id)) {
			return false;
		}
		if (! is_numeric ($option_id)) {
			return false;
		}

		db_execute ('delete from siteshop_product_option where product_id = ? and option_id = ?', $this->val ('id'), $option_id);
		
		if (empty ($available)) {
			return true;
		}

		db_execute ('insert into siteshop_product_option (product_id, option_id, available) values (?, ?, ?)', $this->val ('id'), $option_id, $available);
		//db_execute ('update siteshop_product_option set available = ? where product_id = ? and option_id = ?', $available, $this->val('id'), $option_id);
		
		return true;
	}

	function getVisibleOptions () {
		$options = db_fetch_array (
			'select * from siteshop_product_option as po, siteshop_option as o where po.product_id = ? and po.option_id = o.id order by o.type asc, o.weight, o.name asc',
			$this->val ('id')
		);
		
		foreach ($options as $k => $o) {
			if (file_exists ('inc/app/siteshop/pix/options/' . $o->id . '.jpg')) {
				$options[$k]->has_thumbnail = 'yes';
				$options[$k]->thumbnail = 'inc/app/siteshop/pix/options/' . $o->id . '.jpg';
			} else {
				$options[$k]->has_thumbnail = 'no';
			}
		}
		
		return $options;
	}

	function getAddToCartForm () {

		$beginning = template_simple ('addtocartform1.spt', array ('id' => $this->val ('id')));
		$middle = '';
		$end = '';

		$options = $this->getVisibleOptions ();
		
		if (empty($options)) {
			return template_simple ('addtocartform_nooptions.spt', array ('id' => $this->val ('id')));
		}
		
		$previous_type = $options[0]->type;
		$data = array ();
		$first_enabled_found = false;
		foreach ($options as $o) {
			if ($o->type != $previous_type) {
				$middle .=  template_simple ('addtocartform2.spt', array ('options' => $data, 'type' => $previous_type));
				$previous_type = $o->type;
				$first_enabled_found = false;
				$data = array ();
				$data[$o->id] = $o;
			} else {
				$data[$o->id] = $o;
			}
			
			if ($o->available == 'yes' && ! $first_enabled_found) {
				$data[$o->id]->selected = true;
				$first_enabled_found = true;
			}
		}
		
		$middle .= template_simple ('addtocartform2.spt', array ('options' => $data, 'type' => $previous_type));

		$end .= template_simple ('addtocartform3.spt');

		return $beginning . $middle . $end;
	}

}

?>