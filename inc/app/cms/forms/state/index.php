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

class EditStateForm extends MailForm {
	function EditStateForm () {
		parent::MailForm ();

		$this->extra = 'id="cms-state-form"';

		//global $cgi;
		//$res = db_fetch ('select * from sitellite_page where id = ?', $cgi->id);
		//if (! $res) {
		//	header ('Location: /index');
		//	exit;
		//}
		global $_document;

		//$t =& $this->addWidget ('section', 'section2');
		//$t->title = intl_get ('Access Settings');

		$t =& $this->addWidget ('status', 'sitellite_status');
		$t->alt = intl_get ('Status');
		$t->setValue ($_document->sitellite_status);
		$help = addslashes (intl_get ('The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the scheduler.'));
		$t->extra = 'id="sitellite_status" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('access', 'sitellite_access');
		$t->alt = intl_get ('Access Level');
		$t->setValue ($_document->sitellite_access);
		$help = addslashes (intl_get ('The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).'));
		$t->extra = 'id="sitellite_access" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('calendar', 'sitellite_startdate');
		$t->alt = intl_get ('Publish On (If Status is "Queued")');
		$t->nullable = true;
		$t->setValue ($_document->sitellite_startdate);

		$t =& $this->addWidget ('calendar', 'sitellite_expirydate');
		$t->alt = intl_get ('Archive On (If Status is "Approved")');
		$t->nullable = true;
		$t->setValue ($_document->sitellite_expirydate);

		$t =& $this->addWidget ('info', 'sitellite_owner');
		$t->alt = intl_get ('Created By');
		$t->setValue ($_document->sitellite_owner);

		$t =& $this->addWidget ('textarea', 'changelog');
		$t->alt = intl_get ('Change Summary');
		$t->rows = 3;
		$t->labelPosition = 'left';
		$help = addslashes (intl_get ('The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this page.'));
		$t->extra = 'id="changelog" style="display: none" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
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

$form = new EditStateForm;
echo $form->run ();

?>
