<?php

loader_import ('xed.Cleaners');
global $cgi;
$cgi->html = the_cleaners ($cgi->html);

page_add_style (site_prefix () . '/js/prompt.css');
//page_add_script (site_prefix () . '/js/prototype.js');
//page_add_script (site_prefix () . '/js/scriptaculous/scriptaculous.js');
//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
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
		header ('Content-Type: text/html; charset=' . intl_charset ());
		echo template_simple ('source_return.spt', array ('ifname' => $i, 'html' => $html));
		exit;
	}
}

?>