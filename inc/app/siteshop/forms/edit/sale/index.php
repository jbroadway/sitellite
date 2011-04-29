<?php

class SiteshopEditSaleForm extends MailForm {
	function SiteshopEditSaleForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/edit/sale/settings.php');
		global $cgi;
		$s = new Sale ($cgi->id);
		page_title (intl_get ('Editing Sale') . ': ' . $s->val ('name'));
		$this->widgets['id']->setValue ($s->val ('id'));
		$this->widgets['name']->setValue ($s->val ('name'));
		$this->widgets['start_date']->setValue ($s->val ('start_date'));
		$this->widgets['until_date']->setValue ($s->val ('until_date'));
	}

	function onSubmit ($vals) {
		$products = trim ($vals['product_id']);
		unset ($vals['product_id']);
		unset ($vals['submit_button']);

		// adjust dates
		$vals['start_date'] = array_shift (explode (' ', $vals['start_date'])) . ' 00:00:00';
		$vals['until_date'] = array_shift (explode (' ', $vals['until_date'])) . ' 23:59:59';

		$s = new Sale ($vals['id']);
		$vals = (object) $vals;
		$s->setCurrent ($vals);
		$s->save ();

		// set all products on sale
		db_execute ('delete from siteshop_sale_product where sale_id = ?', $vals->id);
		parse_str ($products, $prods);
		foreach ($prods as $k => $v) {
			db_execute (
				'insert into siteshop_sale_product values (?, ?, ?)',
				$vals->id,
				$k,
				$v
			);
		}

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-sales-action');
		exit;
	}
}

?>