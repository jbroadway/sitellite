<?php

class BoxchooserTestForm extends MailForm {
	function BoxchooserTestForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/boxchooser/forms/test/settings.php');
	}
	function onSubmit ($vals) {
		info ($vals);
	}
}

?>