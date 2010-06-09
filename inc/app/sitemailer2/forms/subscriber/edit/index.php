<?php

class Sitemailer2SubscriberEditForm extends MailForm {
	function Sitemailer2SubscriberEditForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/subscriber/edit/settings.php');
		page_title ('SiteMailer 2 - Edit Subscriber');
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitemailer2-subscribers-action\'; return false"';

		global $cgi;

		$res = db_single ('select * from sitemailer2_recipient where id = ?', $cgi->_key);
		$this->widgets['email']->setValue ($res->email);
		$this->widgets['firstname']->setValue ($res->firstname);
		$this->widgets['lastname']->setValue ($res->lastname);
		$this->widgets['organization']->setValue ($res->organization);
		$this->widgets['website']->setValue ($res->website);
		$this->widgets['status']->setValue ($res->status);

		$res = db_shift_array ('select newsletter from sitemailer2_recipient_in_newsletter where recipient = ?', $cgi->_key);
		$this->widgets['newsletters']->setValue ($res);
	}

	function onSubmit ($vals) {
		if ($vals['website'] == 'http://') {
			$vals['website'] = '';
		}

		db_execute (
			'update sitemailer2_recipient
			set email = ?, firstname = ?, lastname = ?, organization = ?, website = ?, status = ?
			where id = ?',
			$vals['email'],
			$vals['firstname'],
			$vals['lastname'],
			$vals['organization'],
			$vals['website'],
			$vals['status'],
			$vals['_key']
		);

		$res = db_shift_array ('select newsletter from sitemailer2_recipient_in_newsletter where recipient = ?', $vals['_key']);
		db_execute ('delete from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter not in(' . $vals['newsletters'] . ')', $vals['_key']);
		foreach (explode (',', $vals['newsletters']) as $newsletter) {
			if (in_array ($newsletter, $res)) {
				continue;
			}
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter
					(recipient, newsletter, status_change_time, status)
				values
					(?, ?, now(), "subscribed")',
				$vals['_key'],
				$newsletter
			);
		}

		header ('Location: ' . site_prefix () . '/index/sitemailer2-subscribers-action?_msg=subsaved');
		exit;
	}
}

?>