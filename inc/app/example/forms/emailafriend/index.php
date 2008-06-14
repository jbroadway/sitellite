<?php

class ExampleEmailafriendForm extends MailForm {
	function ExampleEmailafriendForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/example/forms/emailafriend/settings.php');

		$this->widgets['email']->addRule ('contains "@"', 'The email address you are sending to appears to be invalid.');
		$this->widgets['yourEmail']->addRule ('contains "@"', 'The email address you are sending from appears to be invalid.');
	}

	function onSubmit ($vals) {
		if (! empty ($vals['msg'])) {
			$vals['msg'] .= "\n\n";
		}

		// build message
		$message = array (
			'subject' => 'Interesting web site from ' . $vals['yourEmail'],
			'body' => $vals['msg']
				. "Check it out at:\n\n"
				. site_url () . $vals['url']
				. "\n\nCheers!\n\n- " . $vals['yourEmail'],
			'from' => 'From: ' . $vals['yourEmail'],
		);

		if (preg_match ('/,/', $vals['email'])) { // multiple recipients
			foreach (preg_split ('/, ?/', $vals['email']) as $email) {
				if (! @mail (
					$email,
					$message['subject'],
					$message['body'],
					$message['from']
				)) {
					return '<h1>Unknown Mail Transfer Error</h1>'
						. '<p>Your email was unable to be sent at this time.</p>';
				}
			}
		} else { // single recipient
			if (! @mail (
				$vals['email'],
				$message['subject'],
				$message['body'],
				$message['from']
			)) {
				return '<h1>Unknown Mail Transfer Error</h1>'
					. '<p>Your email was unable to be sent at this time.</p>';
			}
		}

        return '<h1>Thank You</h1>'
        	. '<p>Thank you for referring a potential customer to Simian Systems.  We appreciate the recommendation.</p>'
        	. '<p>Regards,<br />The Simian Team</p>'
        	. '<p align="center"><a href="javascript: window.close ()">Close Window</a></p>';
	}
}

?>