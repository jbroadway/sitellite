<?php

loader_import ('saf.MailForm');

class AddForm extends MailForm {
	function AddForm () {
		parent::MailForm ();

		global $cgi;

		$this->addWidget ('hidden', 'appname');
		$this->addWidget ('hidden', 'lang');

		$w =& $this->addWidget ('text', 'filename');
		$w->alt = intl_get ('File Name (ie. 001-about-myApp)');
		$w->addRule ('not empty', intl_get ('You must specify a file name.'));
		$w->addRule ('not contains ".."', intl_get ('Your file name contains invalid characters.'));
		$w->addRule ('not exists "inc/app/' . $cgi->appname . '/docs/' . $cgi->lang . '"', intl_get ('The specified file name already exists.  Please choose another or edit that file.'));

		$w =& $this->addWidget ('text', 'title');
		$w->alt = intl_get ('Title');
		$w->addRule ('not empty', intl_get ('You must specify a title.'));

		session_set ('imagechooser_path', '/inc/app/' . $cgi->appname . '/pix');
		$this->extra = 'onsubmit="xed_copy_value (this, \'body\')"';
		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');

		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));
		$b->extra = 'onclick="onbeforeunload_form_submitted=true;"';

		$b =& $w->addButton ('submit_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="onbeforeunload_form_submitted=true; window.location.href = \'' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $cgi->appname . '&lang=' . $cgi->lang . '\'; return false"';
	}

	function onSubmit ($vals) {
		loader_import ('saf.File');

		if (! preg_match ('/\.html$/', $vals['filename'])) {
			$vals['filename'] .= '.html';
		}

		$vals['body'] = '<h1>' . $vals['title'] . '</h1>' . NEWLINEx2 . $vals['body'];

		if (! file_overwrite (
			site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang'] . '/' . $vals['filename'],
			$vals['body']
			)) {
			echo '<p>Error: Unable to write to the file.  Please verify your folder permissions.</p>';
			return;
		}

		header ('Location: ' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $vals['appname'] . '&lang=' . $vals['lang']);
		exit;
	}
}

page_title (intl_get ('Adding Help File'));
$form = new AddForm ();
echo $form->run ();

?>
