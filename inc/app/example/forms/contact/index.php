<?php

class ExampleContactForm extends MailForm {
	function ExampleContactForm () {
		parent::MailForm ();

		// load settings file
		$this->parseSettings ('inc/app/example/forms/contact/settings.php');

		// set the page title
		page_title (intl_get ('Contact Us'));
	}

	function onSubmit ($vals) {
		info ($vals);
	}
}

?>