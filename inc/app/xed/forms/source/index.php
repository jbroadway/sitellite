<?php

page_add_style (site_prefix () . '/js/prompt.css');
page_add_script (site_prefix () . '/js/prototype.js');
page_add_script (site_prefix () . '/js/scriptaculous/scriptaculous.js');
page_add_script (site_prefix () . '/js/prompt.js');

class XedSourceForm extends MailForm {
	function XedSourceForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/source/settings.php');
		$this->ifname = $GLOBALS['cgi']->ifname;
	}

	function onSubmit ($vals) {
		$i = $vals['ifname'];
		$html = str_replace (array ("'", "\r", "\n"), array ('\\\'', '\\r', '\\n'), $vals['html']);
		unset ($vals['ifname']);
		unset ($vals['submit_button']);
		echo template_simple ('source_return.spt', array ('ifname' => $i, 'html' => $html));
		exit;
	}
}

?>