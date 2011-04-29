<?php

class Sitemailer2SubscriberAddForm extends MailForm {
	function Sitemailer2SubscriberAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/subscriber/add/settings.php');
		page_title ('SiteMailer 2 - Add Subscriber');
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitemailer2-subscribers-action\'; return false"';
	}

	function onSubmit ($vals) {
		if ($vals['website'] == 'http://') {
			$vals['website'] = '';
		}

		db_execute (
			'insert into sitemailer2_recipient
				(id, email, firstname, lastname, organization, website, created, status)
			values
				(null, ?, ?, ?, ?, ?, now(), "active")',
			$vals['email'],
			$vals['firstname'],
			$vals['lastname'],
			$vals['organization'],
			$vals['website']
		);

		$id = db_lastid ();

		foreach (explode (',', $vals['newsletters']) as $newsletter) {
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter
					(recipient, newsletter, status_change_time, status)
				values
					(?, ?, now(), "subscribed")',
				$id,
				$newsletter
			);
		}

		header ('Location: ' . site_prefix () . '/index/sitemailer2-subscribers-action?_msg=subcreated');
		exit;
	}
}

?>