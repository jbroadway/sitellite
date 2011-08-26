<?php

loader_import ('siteshop.Objects');

class SiteshopAddOptionForm extends MailForm {
	function SiteshopAddOptionForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/add/option/settings.php');
		page_title (intl_get ('Add an Option'));
	}

	function onSubmit ($vals) {

		unset ($vals['submit_button']);

		$image = $vals['image'];
		unset ($vals['image']);

		$o = new Option;
		$res = $o->add($vals);

		if (is_object ($image) && preg_match ('/\.jpg$/i', $image->name)) {
			$image->move ('inc/app/siteshop/pix/options', $res . '.jpg');
		}

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-options-action');
		exit;
	}
}

?>