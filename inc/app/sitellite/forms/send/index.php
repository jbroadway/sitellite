<?php

class SitelliteSendForm extends MailForm {
	function SitelliteSendForm () {
		parent::MailForm (__FILE__);
		$user = session_get_user ();
		$this->widgets['from_email']->setValue ($user->email);
		$this->widgets['from_name']->setValue ($user->firstname . ' ' . $user->lastname);
		$groups = array ('' => '- All -');
		foreach (db_pairs ('select id, name from sitellite_form_type order by name asc') as $k => $v) {
			$groups[$k] = $v;
		}
		$this->widgets['send_to']->setValues ($groups);
		page_title (intl_get ('Send Email'));
	}

	function onSubmit ($vals) {
		$sql = 'select distinct email_address from sitellite_form_submission';
		if ($vals['include_no_consent']) {
			$sql .= ' where (may_we_contact_you is null or may_we_contact_you = "yes")';
		} else {
			$sql .= ' where may_we_contact_you = "yes"';
		}
		if ($vals['send_to']) {
			$sql .= ' and form_type = ' . db_quote ($vals['send_to']);
		}
		$emails = db_shift_array ($sql);

		set_time_limit (0);

		foreach ($emails as $email) {
			// send email
			@mail (
				$email,
				$vals['subject'],
				$vals['message'],
				'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n"
			);
		}

		// send copy to sender
		@mail (
			$vals['from_email'],
			$vals['subject'],
			$vals['message'],
			'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n"
		);

		page_title (intl_get ('Email Sent'));
		echo '<p>' . intl_get ('Email sent to') . ' ' . count ($emails) . ' ' . intl_get ('recipients') . '.</p>';
		echo '<p><a href="' . site_prefix () . '/index/cms-browse-action?collection=sitellite_form_submission">Continue</a></p>';
	}
}

?>