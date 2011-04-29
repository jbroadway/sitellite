<?php

class PetitionSignForm extends MailForm {
	function PetitionSignForm () {
		parent::MailForm (__FILE__);
		$this->action = site_prefix () . '/index/petition-sign-form';
		global $cgi;
		$this->widgets['id']->setValue ($cgi->id);
		$this->widgets['submit_button']->setValues (intl_get ('Submit now'));
	}
	function onSubmit ($vals) {
		db_execute (
			'insert into petition_signature values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())',
			$vals['id'],
			$vals['firstname'],
			$vals['lastname'],
			$vals['email'],
			$vals['address'],
			$vals['city'],
			$vals['province'],
			$vals['country'],
			$vals['postal_code']
		);

		page_title (intl_get ('Signature Added'));
		echo template_simple ('signed.spt', $vals);
	}
}

?>