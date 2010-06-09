<?php

class SiteshopAddOfferForm extends MailForm {
	function SiteshopAddOfferForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/add/offer/settings.php');
		page_title (intl_get ('Add a Checkout Offer'));
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		$n = db_shift ('select offer_number from siteshop_checkout_offer order by offer_number desc limit 1');
		$vals['offer_number'] = $n + 1;

		$s = new CheckoutOffer ($vals);

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-offers-action');
		exit;
	}
}

?>