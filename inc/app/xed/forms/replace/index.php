<?php

class XedReplaceForm extends MailForm {
	function XedReplaceForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/replace/settings.php');
		$this->widgets['find']->setValue (session_get ('xed_source_find'));
		$this->widgets['replace']->setValue (session_get ('xed_source_replace'));
	}

	function onSubmit ($vals) {
		session_set ('xed_source_find', $vals['find']);
		session_set ('xed_source_replace', $vals['replace']);
		$vals['find'] = str_replace (array ('\\', '\''), array ('\\\\', '\\\''), $vals['find']);
		$vals['replace'] = str_replace (array ('\\', '\''), array ('\\\\', '\\\''), $vals['replace']);
		echo template_simple ('replace_return.spt', $vals);
		exit;
	}
}

?>