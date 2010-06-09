<?php

class SiteshopEditOfferForm extends MailForm {
	function SiteshopEditOfferForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/edit/offer/settings.php');
		global $cgi;
		$s = new CheckoutOffer ($cgi->id);
		$this->widgets['id']->setValue ($cgi->id);
		$this->widgets['offer_number']->setValue ($s->val ('offer_number'));
		$this->widgets['offer_text']->setValue ($s->val ('offer_text'));
		$this->widgets['product_id']->setValue ($s->val ('product_id'));
		$this->widgets['sale_price']->setValue ($s->val ('sale_price'));
		page_title (intl_get ('Editing Checkout Offer') . ': ' . $s->val ('offer_number'));
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		$s = new CheckoutOffer ($vals['id']);
		$vals = (object) $vals;
		$s->setCurrent ($vals);
		$s->save ();

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-offers-action');
		exit;
	}
}

?>