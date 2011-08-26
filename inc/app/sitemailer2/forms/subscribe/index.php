<?php

class SubscribeForm extends MailForm {
	function SubscribeForm () {
		parent::MailForm ();

		global $cgi;

		// gather just the email address
		$w =& $this->addWidget ('text', 'email');
		$w->alt = intl_get ('Email Address');
		$w->addRule ('contains "@"', 'Your email address does not appear to be valid.');
		$w->addRule ('func "sitemailer_unique_check"', 'The email address specified is already a subscriber.');

		if (appconf ('collect_info')) {
			// gather firstname, lastname, organization, website

			$w =& $this->addWidget ('text', 'firstname');
			$w->alt = intl_get ('First Name');
			$w->addRule ('not empty', 'You must enter your first name.');

			$w =& $this->addWidget ('text', 'lastname');
			$w->alt = intl_get ('Last Name');
			$w->addRule ('not empty', 'You must enter your last name.');

			$w =& $this->addWidget ('text', 'organization');
			$w->alt = intl_get ('Organization');
			$w->addRule ('not empty', 'You must enter your organization name.');

			$w =& $this->addWidget ('text', 'website');
			$w->alt = intl_get ('Web Site');
			$w->setDefault ('http://');
			$w->addRule ('not empty', 'You must enter your web site address.');
			$w->addRule ('not is "http://"', 'You must enter your web site address.');

		} else {
			$this->addWidget ('hidden', 'firstname');
			$this->addWidget ('hidden', 'lastname');
			$this->addWidget ('hidden', 'organization');
			$this->addWidget ('hidden', 'website');
		}

		$w =& $this->addWidget ('hidden', 'group');
		if (empty ($cgi->group)) {
			$cgi->group = '1';
			$w->setValue ('1');
		}

		$w =& $this->addWidget ('submit', 'submit_button');
		$w->setValues (intl_get ('Subscribe'));

	}

	function onSubmit ($vals) {
		if (sitemailer_subscriber_exists ($vals['email'])) {
			// subscriber is being added to a second group
			db_execute ('update sitemailer_subscriber set status = ? where email = ?', 'subscribed', $vals['email']);
			db_execute ('insert into sitemailer_subscriber_category (id, subscriber, category) values (null, ?, ?)', $vals['email'], $vals['group']);

			// say thank you, johnny
			page_title (intl_get ('Thank You!'));
			echo template_simple ('responses/subscribed.spt');

		} else {
			// add user and subscribe them
			$res = db_execute (
				'insert into sitemailer_subscriber
					(email, firstname, lastname, organization, website, registered, status)
				values
					(?, ?, ?, ?, ?, now(), ?)',
				$vals['email'],
				$vals['firstname'],
				$vals['lastname'],
				$vals['organization'],
				$vals['website'],
				'unverified' // first timers need to verify their email address
			);

			$res = db_execute (
				'insert into sitemailer_subscriber_category
					(id, subscriber, category)
				values
					(null, ?, ?)',
				$vals['email'],
				$vals['group']
			);

			// make validation key (md5 of email.registered)
			$vals['key'] = sitemailer_make_verification_key ($vals['email']);
			$vals['from_name'] = appconf ('from_name');
			$vals['from_email'] = appconf ('from_email');

			// send email with validation link
			mail ($vals['email'], intl_get ('Subscription Verification'), template_simple ('responses/email_confirmation.spt', $vals), 'From: ' . $vals['from_email']);

			// say 'check your email'
			page_title (intl_get ('Thank You!'));
			echo template_simple ('responses/checkyouremail.spt', $vals);
		}
	}
}

function sitemailer_make_verification_key ($email) {
	$reg = db_shift ('select registered from sitemailer_subscriber where email = ?', $email);
	return md5 ($email . $reg);
}

function sitemailer_subscriber_exists ($email) {
	return db_shift ('select count(*) from sitemailer_subscriber where email = ?', $email);
}

function sitemailer_unique_check ($vals) {
	global $cgi;

	// check for uniqueness of email address in the specified group
	$res = db_shift ('select count(*) from sitemailer_subscriber s, sitemailer_subscriber_category c where s.email = c.subscriber and s.email = ? and c.category = ? and s.status = ?', $cgi->email, $cgi->group, 'subscribed');
	if (! $res || $res === 0) {
		return true;
	}
	return false;
}

page_title (intl_get ('Subscribe'));
$form = new SubscribeForm ();
echo $form->run ();

?>