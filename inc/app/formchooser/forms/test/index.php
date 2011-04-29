<?php

class FormchooserTestForm extends MailForm {
	function FormchooserTestForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/formchooser/forms/test/settings.php');
	}
	function onSubmit ($vals) {
		info ($vals);
	}
}

?>