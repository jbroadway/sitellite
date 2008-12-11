<?php

class ExampleEmailafriendForm extends MailForm {
	function ExampleEmailafriendForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/example/forms/emailafriend/settings.php');
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
				. "\n\n- " . $vals['yourEmail'],
			'from' => 'From: ' . $vals['yourEmail'],
		);

		if (! @mail (
			$vals['email'],
			$message['subject'],
			$message['body'],
			$message['from']
		)) {
			page_title (intl_get ('Unknown Error'));
			return '<p>' . intl_get ('Your email was unable to be sent at this time.') . '</p>';
		}

		page_title (intl_get ('Thank You'));
        return '<p>' . intl_get ('Your message has been sent.') . '</p>';
	}
}

?>