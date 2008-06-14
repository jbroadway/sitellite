<?php

class XedFullscreenForm extends MailForm {
	function XedFullscreenForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/fullscreen/settings.php');

		global $cgi;

		page_onunload ('xed_fullscreen_copy_value ()');

		page_add_script ('
			function xed_fullscreen_copy_value (form, field) {
				s = xed_get_source ("xeditor");
				opener.document.getElementById ("' . $cgi->ifname . '").contentWindow.document.body.innerHTML = s;
				window.close ();
			}
		');

		page_add_style ('
			.content {
				padding-right: 0px ! important;
				padding-left: 0px ! important;
				padding-top: 3px ! important;
				padding-bottom: 5px ! important;
			}
		');
	}

	function onSubmit ($vals) {
		page_onload (false);
		page_onclick (false);
		page_onfocus (false);
	}
}

?>