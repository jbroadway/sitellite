<?php

class SiteshopAddSaleForm extends MailForm {
	function SiteshopAddSaleForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/add/sale/settings.php');
		page_title (intl_get ('Add a Sale'));
	}

	function onSubmit ($vals) {
		$products = trim ($vals['product_id']);
		unset ($vals['product_id']);
		unset ($vals['submit_button']);

		// adjust dates
		$vals['start_date'] = array_shift (explode (' ', $vals['start_date'])) . ' 00:00:00';
		$vals['until_date'] = array_shift (explode (' ', $vals['until_date'])) . ' 23:59:59';

		$s = new Sale ($vals);

		// set all products on sale
		db_execute ('delete from siteshop_sale_product where sale_id = ?', $s->val ('id'));
		parse_str ($products, $prods);
		foreach ($prods as $k => $v) {
			db_execute (
				'insert into siteshop_sale_product values (?, ?, ?)',
				$s->val ('id'),
				$k,
				$v
			);
		}

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-sales-action');
		exit;
	}
}

?>