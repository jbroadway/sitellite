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

class EditPageForm extends MailForm {
	function EditPageForm () {
		parent::MailForm ();

		global $page;

		$this->extra = 'id="cms-edit-form" onsubmit="xed_copy_value (this, \'body\')"';

		$t =& $this->addWidget ('text', 'title');
		$t->alt = intl_get ('Page Title');
		$t->addRule ('not empty', 'You must enter a title for your page.');
		$t->extra = 'size="40"';

		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
		if (appconf ('tidy_path')) {
			$w->tidy_path = appconf ('tidy_path');
		}
		$w->addRule ('not empty', 'You must enter content into your page body.');

		//global $cgi;
		//$res = db_fetch ('select title, body from sitellite_page where id = ?', $cgi->id);
		//if (! $res) {
		//	header ('Location: /index');
		//	exit;
		//}
		global $_document;

		$t->setValue ($_document->title);
		$w->setValue ($_document->body);
		page_title ('Sitellite - Editing Page: ' . $res->title);
		//$this->title = 'Sitellite - Editing Page: ' . $res->title;

		$w =& $this->addWidget ('hidden', 'id');

/*
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues ('Save');

		$b =& $w->addButton ('submit_button', 'Preview');
		$b->extra = 'onclick="return cms_preview (this.form)"';

		$b =& $w->addButton ('submit_button', 'Cancel');
		$b->extra = 'onclick="return cms_cancel (this.form)"';
*/
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rev');

		$rev = new Rev; // default: database, database

		unset ($vals['submit_button']);

		$res = $rev->modify ('sitellite_page', 'id', $vals['id'], $vals);

		if (! $res) {
			die ($rev->error);
		} else {
			header ('Location: ' . site_prefix () . '/index/' . $vals['id']);
			exit;
		}
	}
}

echo CMS_JS_PREVIEW;
echo CMS_JS_CANCEL;

$form = new EditPageForm;
echo $form->run ();

?>