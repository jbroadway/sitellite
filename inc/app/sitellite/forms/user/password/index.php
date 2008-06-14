<?php

class PasswordForm extends MailForm {
	function PasswordForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitellite/forms/user/password/settings.php');
		$this->widgets['pw2']->addRule ('equals "pw1"', 'Your passwords do not match.');
	}

	function onSubmit ($vals) {
	}
}

if ($context == 'action') {
	page_title ('Change Password');
}
$form = new PasswordForm ();
echo $form->run ();

?>