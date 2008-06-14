<?php

$on = appconf ('contact');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'form:sitemember/contact') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

class SitememberContactForm extends MailForm {
	function SitememberContactForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitemember/forms/contact/settings.php');

		global $cgi;

		if (! isset ($cgi->user)) {
			header ('Location: ' . site_prefix () . '/index/sitemember-app');
			exit;
		}

		$this->member = session_get_user ($cgi->user);
		if (! is_object ($this->member) || $this->member->public != 'yes') {
			header ('Location: ' . site_prefix () . '/index/sitemember-app');
			exit;
		}

		page_title (intl_get ('Member Contact Form') . ': ' . $cgi->user);

		if (session_valid ()) {
			$info = session_get_user ();
			$this->widgets['email']->setValue ($info->email);
		}
	}

	function onSubmit ($vals) {
		if (! @mail ($this->member->email, $vals['subject'], $vals['message'], 'From: ' . $vals['email'])) {
			page_title ('Unknown Error');
			echo '<p>' . intl_get ('An error occurred trying to send the message.  Please try again later.') . '</p>';
			return;
		}

		page_title (intl_get ('Message Sent'));
		echo '<p>' . intl_get ('Your message has been sent.') . '</p>';
	}
}

?>