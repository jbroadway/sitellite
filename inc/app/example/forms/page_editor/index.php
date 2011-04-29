<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

class PageEditorForm extends MailForm {
	function PageEditorForm () {
		parent::MailForm ();

		// parse the form settings file to create some of its fields
		$this->parseSettings ('inc/app/example/forms/page_editor/settings.php');

		// give it some default values
		$this->widgets['title']->setDefault ('Test Page');
		$this->widgets['body']->setDefault ('<p>Foo bar</p>' . NEWLINEx2 . '<p>Qwerty</p>');

		// set up the submit buttons
		$w =& $this->addWidget ('msubmit', 'msubmit');

		$b1 =& $w->getButton ();
		$b1->setValues ('Save');

		$b2 =& $w->addButton ('preview');
		$b2->setValues ('Preview');

		// call the javascript for page previewing, found in the associated template
		$b2->extra = 'onclick="return page_preview (this.form)"';
	}

	function onSubmit ($vals) {
		// here is where we handle the form.
		// in this case, we simply display its output to the screen
		page_title ($vals['title']);
		return $vals['body'];
	}
}

// include the preview javascript template and set the page title
echo template_simple ('page_editor.spt');
page_title (intl_get ('Page Editor'));

// load and run the form
$form = new PageEditorForm;
echo $form->run ();

?>