<?php

class SiteinvoiceAddClientForm extends MailForm {
	function SiteinvoiceAddClientForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteinvoice/forms/add/client/settings.php');

		page_title ('SiteInvoice - Adding Client');
	}

	function onSubmit ($vals) {
		db_execute (
			'insert into siteinvoice_client
				(id, code, name, contact_name, contact_email, contact_phone, address)
			values
				(null, ?, ?, ?, ?, ?, ?)',
			$vals['code'],
			$vals['name'],
			$vals['contact_name'],
			$vals['contact_email'],
			$vals['contact_phone'],
			$vals['address']
		);

		page_title ('SiteInvoice - Client Added');

		echo '<p><a href="' . site_prefix () . '/index/siteinvoice-clients-action">Continue</a></p>';
	}
}

?>
