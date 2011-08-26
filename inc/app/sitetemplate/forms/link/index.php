<?php

class SitetemplateLinkForm extends MailForm {
	function SitetemplateLinkForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitetemplate/forms/link/settings.php');
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);
		echo template_simple ('link_return.spt', array ('vals' => $vals));
		exit;
	}
}

?>