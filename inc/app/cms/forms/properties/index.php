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

class EditPropertiesForm extends MailForm {
	function EditPropertiesForm () {
		parent::MailForm ();

		//global $page;
		//$page->onload = 'xed_init (\'body\')';
		//$page->onclick = 'checkModal()';
		//$page->onfocus = 'return checkModal()';

		//$this->extra = 'onsubmit="xed_copy_value (this, \'body\')"';
		$this->extra = 'id="cms-properties-form"';

		//global $cgi;
		//$res = db_fetch ('select * from sitellite_page where id = ?', $cgi->id);
		//if (! $res) {
		//	header ('Location: /index');
		//	exit;
		//}
		global $_document;

		$t =& $this->addWidget ('section', 'section1');
		$t->title = intl_get ('Display Settings');

		$t =& $this->addWidget ('ref', 'below_page');
		$t->table = 'sitellite_page';
		$t->primary_key = 'id';
		$t->display_column = 'title';
		$t->ref_column = 'below_page';
		$t->self_ref = true;
		$t->nullable = true;
		$t->alt = intl_get ('Location in Web Site');
		$t->setValue ($_document->below_page);
		$help = addslashes (intl_get ('Choose the page that this page should appear under in the hierarchy of the web site.'));
		$t->extra = 'id="below_page" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('cms.Widget.Template', 'template');
		$t->alt = intl_get ('Display with Template');
		$t->setValue ($res->template);
		$help = addslashes (intl_get ('Choose which template you want this page to be displayed with.  This changes the look and feel of the page.'));
		$t->extra = 'id="template" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('select', 'include');
		$t->alt = intl_get ('Include in Site Navigation?');
		$t->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$t->setValue ($_document->include);
		$help = addslashes (intl_get ('This determines whether or not you want the page to appear in the web site menus and site maps.'));
		$t->extra = 'id="include" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('select', 'is_section');
		$t->alt = intl_get ('Is This a Section Index?');
		$t->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$t->setValue ($_document->is_section);
		$help = addslashes (intl_get ('If you make this page a section index, then pages below it will adopt its template settings if theirs is not specified explicitly.  This allows you to give a consistent look and feel to entire sections of your web site.'));
		$t->extra = 'id="is_section" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('text', 'external');
		$t->alt = intl_get ('Alias of (a URL)');
		$t->setValue ($_document->external);
		$help = addslashes (intl_get ('If you provide a link to an external web page or file (ie. a PDF or Word document), then this page can act as an alias for that resource within your web site navigation.'));
		$t->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

/*
		$t =& $this->addWidget ('section', 'section2');
		$t->title = intl_get ('Access Settings');

		$t =& $this->addWidget ('status', 'sitellite_status');
		$t->alt = intl_get ('Status');
		$t->setValue ($res->sitellite_status);

		$t =& $this->addWidget ('access', 'sitellite_access');
		$t->alt = intl_get ('Access Level');
		$t->setValue ($res->sitellite_access);

		$t =& $this->addWidget ('calendar', 'sitellite_startdate');
		$t->alt = intl_get ('Publish On (If Status is "Queued")');
		$t->nullable = true;
		$t->setValue ($res->sitellite_startdate);

		$t =& $this->addWidget ('calendar', 'sitellite_expirydate');
		$t->alt = intl_get ('Archive On (If Status is "Approved")');
		$t->nullable = true;
		$t->setValue ($res->sitellite_expirydate);

		$t =& $this->addWidget ('info', 'sitellite_owner');
		$t->alt = intl_get ('Created By');
		$t->setValue ($res->sitellite_owner);
*/

		$t =& $this->addWidget ('section', 'section3');
		$t->title = intl_get ('Page Attributes');

		$t =& $this->addWidget ('cms.Widget.Keywords', 'keywords');
		$t->alt = intl_get ('Keywords (Comma-Separated List)');
		$t->setValue ($_document->keywords);
		$help = addslashes (intl_get ('Select the keywords from the list that describe the current page, or you can add or remove keywords using the Add and Remove buttons.  Keywords help target your page to its intended audience in search engines and site searches.'));
		$t->extra = 'id="keywords" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('textarea', 'description');
		$t->alt = intl_get ('Description');
		$t->setValue ($_document->description);
		$t->rows = 3;
		$t->labelPosition = 'left';
		$help = addslashes (intl_get ('A description helps target your page to its intended audience in search engines and site searches performed by visitors.'));
		$t->extra = 'id="description" style="overflow: hidden" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		//$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
		//$w->tidy_path = '/sw/bin/tidy';
		//$w->addRule ('not empty', 'You must enter content into your page body.');

		//page_title ('Sitellite - Page Properties: ' . $res->title);
		//$this->title = 'Sitellite - Editing Page: ' . $res->title;

		$w =& $this->addWidget ('hidden', 'id');

/*
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues ('Save');

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
//echo CMS_JS_CANCEL;

$form = new EditPropertiesForm;
echo $form->run ();

?>
