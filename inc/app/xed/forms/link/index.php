<?php

class XedLinkForm extends MailForm {
	function XedLinkForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/link/settings.php');
	}

	function onSubmit ($vals) {
		$i = $vals['ifname'];
		unset ($vals['ifname']);
		unset ($vals['submit_button']);
		echo template_simple ('link_return.spt', array ('ifname' => $i, 'vals' => $vals));
		exit;
	}
}

?>