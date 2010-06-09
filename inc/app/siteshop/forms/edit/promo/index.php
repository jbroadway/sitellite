<?php

class SiteshopEditPromoForm extends MailForm {
	function SiteshopEditPromoForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/edit/promo/settings.php');
		global $cgi;
		$s = new Promo ($cgi->id);
		page_title (intl_get ('Editing Promo Code') . ': ' . $s->val ('code'));
		$this->widgets['id']->setValue ($s->val ('id'));
		$this->widgets['code']->setValue ($s->val ('code'));
		$this->widgets['discount_type']->setValue ($s->val ('discount_type'));
		$this->widgets['discount']->setValue ($s->val ('discount'));
		$this->widgets['expires']->setValue ($s->val ('expires'));
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		$s = new Promo ($vals['id']);
		$vals = (object) $vals;
		$s->setCurrent ($vals);
		$s->save ();

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-promo-action');
		exit;
	}
}

?>