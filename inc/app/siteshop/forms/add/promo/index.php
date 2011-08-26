<?php

class SiteshopAddPromoForm extends MailForm {
	function SiteshopAddPromoForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/add/promo/settings.php');
		page_title (intl_get ('Add a Promo Code'));
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		$s = new Promo ($vals);

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-promo-action');
		exit;
	}
}

?>