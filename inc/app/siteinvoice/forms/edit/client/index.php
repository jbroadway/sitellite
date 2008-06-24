<?php

class SiteinvoiceEditClientForm extends MailForm {
	function SiteinvoiceEditClientForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteinvoice/forms/edit/client/settings.php');

		global $cgi;

		page_title ('SiteInvoice - Editing Client: ' . $cgi->id);

		$client = db_single ('select * from siteinvoice_client where id = ?', $cgi->id);

		foreach (get_object_vars ($client) as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		db_execute (
			'update siteinvoice_client
			set code = ?, name = ?, contact_name = ?, contact_email = ?, contact_phone = ?, address = ?
			where id = ?',
			$vals['code'],
			$vals['name'],
			$vals['contact_name'],
			$vals['contact_email'],
			$vals['contact_phone'],
			$vals['address'],
			$vals['id']
		);

		page_title ('SiteInvoice - Client Updated');

		echo '<p><a href="' . site_prefix () . '/index/siteinvoice-clients-action">Continue</a></p>';
	}
}

?>
