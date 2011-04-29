<?php

loader_import ('siteshop.Objects');

class SiteshopEditOptionForm extends MailForm {
	function SiteshopEditOptionForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/edit/option/settings.php');
		page_title (intl_get ('Edit an Option'));

		global $cgi;
		$o = new Option($cgi->o);
		$this->action = site_prefix() . '/index/siteshop-edit-option-form?o=' . $cgi->o;

		//$this->widgets['type']->setValue($o->val ('type'));
		$this->widgets['name']->setValue($o->val ('name'));
		$this->widgets['weight']->setValue($o->val ('weight'));
		$this->widgets['value']->setValue($o->val('value'));
		$this->widgets['type']->setValue($o->val('type'));
		$this->widgets['image']->setValue($o->getImage());
	}

	function onSubmit ($vals) {

		global $cgi;

		unset ($vals['submit_button']);
		$vals['id'] = $cgi->o;

		$image = $vals['image'];
		unset ($vals['image']);

		$o = new Option($cgi->o);
		$vals = (object) $vals;


		//info ($vals);exit;
		$o->setCurrent ($vals);
		$o->save();

		if (is_object ($image) && preg_match ('/\.jpg$/i', $image->name)) {
			unlink ('inc/app/siteshop/pix/options/' . $o->val ('id') . '.jpg');
			$image->move ('inc/app/siteshop/pix/options', $o->val ('id') . '.jpg');
		}

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-options-action');
		exit;
	}
}

?>