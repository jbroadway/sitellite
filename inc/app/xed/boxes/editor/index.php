<?php

loader_import ('saf.MailForm');

class WysiwygEditorForm extends MailForm {
	function WysiwygEditorForm () {
		parent::MailForm ();

		$this->addWidget ('hidden', 'field_name');
		$this->addWidget ('hidden', 'form_name');

		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');

		$w =& $this->addWidget ('msubmit', 'submit_button');
		$b =& $w->getButton ();
		$b->setValues (intl_get ('Submit'));
		$b->extra = 'onclick="return xed_window_submit (this.form)"';
		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="return xed_window_cancel (this.form)"';
	}

	function show () {
		echo '<script language="javascript" type="text/javascript">
			function xed_window_submit (f) {
				opener.document.forms[f.elements["form_name"].value].elements[f.elements["field_name"].value].value = xed_get_source ("body");
				window.close ();
				return false;
			}
			function xed_window_cancel (f) {
				window.close ();
				return false;
			}
		</script>';
		return parent::show ();
	}

	function onSubmit ($vals) {
		echo '<script language="javascript" type="text/javascript">window.close ()</script>';
		exit;
	}
}

global $cgi;

page_title (intl_get ('WYSIWYG Mode'));

if (isset ($cgi->template)) {
	page_template ($cgi->template);
}

$form = new WysiwygEditorForm;
echo $form->run ();

?>