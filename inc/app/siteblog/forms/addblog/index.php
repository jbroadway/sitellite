<?php

if (! session_admin ()) {
    header ('Location: ' . site_prefix () . '/index');
    exit;
}

class SiteblogAddblogForm extends MailForm {
	function SiteblogAddblogForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteblog/forms/addblog/settings.php');
	}

	function onSubmit ($vals) {
		// your handler code goes here
	}
}

?>
