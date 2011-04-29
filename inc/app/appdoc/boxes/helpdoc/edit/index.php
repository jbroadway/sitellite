<?php

loader_import ('saf.MailForm');
loader_import ('help.Help');

class EditForm extends MailForm {
	function EditForm () {
		parent::MailForm ();

		global $cgi, $_helpdoc;

		$this->addWidget ('hidden', 'appname');
		$this->addWidget ('hidden', 'lang');
		$this->addWidget ('hidden', 'helpfile');

		$w =& $this->addWidget ('text', 'filename');
		$w->alt = intl_get ('File Name (ie. 001-about-myApp)');
		$w->addRule ('not empty', intl_get ('You must specify a file name.'));
		$w->addRule ('not contains ".."', intl_get ('Your file name contains invalid characters.'));
		$w->setValue ($cgi->helpfile);

		$w =& $this->addWidget ('text', 'title');
		$w->alt = intl_get ('Title');
		$w->addRule ('not empty', intl_get ('You must specify a title.'));
		$w->setValue ($_helpdoc->title);

		session_set ('imagechooser_path', 'inc/app/' . $cgi->appname . '/pix');
		$this->extra = 'onsubmit="xed_copy_value (this, \'body\')"';
		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
		$w->setValue ($_helpdoc->body);

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

		if (! preg_match ('/\.html$/', $vals['helpfile'])) {
			$vals['helpfile'] .= '.html';
		}

		$vals['body'] = '<h1>' . $vals['title'] . '</h1>' . NEWLINEx2 . $vals['body'];

		if (! file_overwrite (
			site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang'] . '/' . $vals['filename'],
			$vals['body']
			)) {
			echo '<p>Error: Unable to write to the file.  Please verify your file and folder permissions.</p>';
			return;
		}

		if ($vals['helpfile'] != $vals['filename']) {
			// erase old file, this is a rename
			$res = @unlink (site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang'] . '/' . $vals['helpfile']);
			if (! $res) {
				echo '<p>Error: Unable to remove the old file.  Please verify your file and folder permissions.</p>';
				return;
			}
		}

		header ('Location: ' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $vals['appname'] . '&lang=' . $vals['lang']);
		exit;
	}
}

$GLOBALS['_helpdoc'] = new StdClass;
global $cgi, $_helpdoc;

loader_import ('help.Help');

$_helpdoc->body = @join ('', @file ('inc/app/' . $cgi->appname . '/docs/' . $cgi->lang . '/' . $cgi->helpfile . '.html'));
$_helpdoc->title = help_get_title ($_helpdoc->body, $cgi->helpfile);
$_helpdoc->body = preg_replace ('/<h[1-6][^>]*>([^<]+)<\/h[1-6]>[\r\n]*/i', '', $_helpdoc->body, 1);

page_title (intl_get ('Editing Help File') . ': ' . $cgi->title);

$form = new EditForm ();
echo $form->run ();

?>
