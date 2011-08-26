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

class CmsAddSitellite_pageForm extends MailForm {
	function CmsAddSitellite_pageForm () {
		parent::MailForm ();

		page_title (intl_get ('Adding Page'));

		global $page, $cgi;

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		$this->extra = 'id="cms-edit-form" onsubmit="xed_copy_value (this, \'body\')"';

		// include formhelp
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script ('
			formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
			formhelp_append = \'</td></tr></table>\';

			function cms_preview_action (f) {
				cms_copy_values (f);
				return cms_preview (f);
			}
			
			function cms_cancel_action (f) {
				cms_copy_values (f);
				if (confirm (\'Are you sure you want to cancel?\')) {
					return cms_cancel (f);
				}
				return false;
			}

			function page_id () {
				f = document.forms[0];
				if (f.elements[\'id\'].value.length == 0) {
					sugg_id = f.elements[\'title\'].value.toLowerCase ();
					sugg_id = sugg_id.replace (/[àáâäå]/g, \'a\');
					sugg_id = sugg_id.replace (/[ç]/g, \'c\');
					sugg_id = sugg_id.replace (/[éèêë]/g, \'e\');
					sugg_id = sugg_id.replace (/[íìîï]/g, \'i\');
					sugg_id = sugg_id.replace (/[ñ]/g, \'n\');
					sugg_id = sugg_id.replace (/[óòôöø]/g, \'o\');
					sugg_id = sugg_id.replace (/[úùûüů]/g, \'u\');
					sugg_id = sugg_id.replace (/[ÿ]/g, \'y\');
					sugg_id = sugg_id.replace (/^[^a-z0-9_-]+/g, \'\');
					sugg_id = sugg_id.replace (/[^a-z0-9_-]+$/g, \'\');
					sugg_id = sugg_id.replace (/[^a-z0-9_-]+/g, \'-\');
					sugg_id = sugg_id.replace (/-+/g, \'-\');
					f.elements[\'id\'].value = sugg_id;
				}
			}

			function page_id_to_lower () {
				f = document.forms[0];
				f.elements[\'id\'].value = f.elements[\'id\'].value.toLowerCase ();
			}
		');
		if (session_pref ('form_help') == 'off') {
			page_add_script ('
				formhelp_disable = true;
			');
		}

		$w =& $this->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Edit');

		// edit widgets go here

		$w =& $this->addWidget ('hidden', 'collection');
		$w =& $this->addWidget ('hidden', '_return');

		$w =& $this->addWidget ('text', 'title');
		$w->alt = intl_get ('Page Title');
		//$w->addRule ('not empty', 'You must enter a title for your page.');
		$help = addslashes (intl_get ('The standard title of the web page, usually used in the content body as a top-level heading.'));
		$w->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide (); page_id ()"';
		$w->length = 128;

		$w =& $this->addWidget ('text', 'id');
		$w->alt = intl_get ('Page ID');
		$w->addRule ('not empty', 'You must enter an ID for your page.');
		$w->addRule ('not regex "[^a-zA-Z0-9_-]"', 'Your ID contains invalid characters.');
		$w->addRule ('unique "sitellite_page/id"', 'A page with this ID already exists.');
		$w->addRule ('func "cms_rule_no_actions"', 'Your page ID cannot end in -action, -app, or -form.');
		$help = addslashes (intl_get ('The unique page identifier, used in the URL to request this page (ie. /index/page_id).  Must contain only letters, numbers, dashes, and underscores (ie. product_info).'));
		$w->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide (); page_id_to_lower ()"';
		$w->length = 72;

		$w =& $this->addWidget ('text', 'nav_title');
		$w->alt = intl_get ('Title in Navigation');
		$help = addslashes (intl_get ('This allows you to specify an alternate title to use when linking to this page.'));
		$w->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$w->length = 128;
		$w->advanced = true;

		$w =& $this->addWidget ('text', 'head_title');
		$w->alt = intl_get ('Window Title');
		$help = addslashes (intl_get ('This allows you to specify an alternate title to use in the header of the document, which will appear in the top bar of the browser window.'));
		$w->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$w->length = 128;
		$w->advanced = true;

// Start: SEMIAS #77 Content appearing outside Xed in IE7.
// ------------------
// $w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
// ------------------
		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
// END: SEMIAS
		if (appconf ('tidy_path')) {
			$w->tidy_path = appconf ('tidy_path');
		}
		$w->addRule ('not empty', 'You must enter content into your page body.');
		//$w->length = 65535;

		$w =& $this->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Properties');

		// property widgets go here

		$t =& $this->addWidget ('section', 'section1');
		$t->title = intl_get ('Display Settings');

		$t =& $this->addWidget ('pagebrowser.Widget.Pagebrowser', 'below_page');
		//$t->table = 'sitellite_page';
		//$t->primary_key = 'id';
		//$t->display_column = 'if(nav_title != "", nav_title, if(title != "", title, id))';
		//$t->ref_column = 'below_page';
		//$t->self_ref = true;
		//$t->addblank = true;
		$t->alt = intl_get ('Location in Web Site');
		$help = addslashes (intl_get ('Choose the page that this page should appear under in the hierarchy of the web site.'));
		$t->extra = 'id="below_page" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('cms.Widget.Templates', 'template');
		$t->alt = intl_get ('Display with Template');
		$help = addslashes (intl_get ('Choose which template you want this page to be displayed with.  This changes the look and feel of the page.'));
		$t->extra = 'id="template" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('select', 'include');
		$t->alt = intl_get ('Include in Site Navigation?');
		$t->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$t->setValue ('yes');
		$help = addslashes (intl_get ('This determines whether or not you want the page to appear in the web site menus and site maps.'));
		$t->extra = 'id="include" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->advanced = true;

		$t =& $this->addWidget ('select', 'include_in_search');
		$t->alt = intl_get ('Include in Search?');
		$t->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$t->setValue ('yes');
		$help = addslashes (intl_get ('This determines whether or not you want the page to be indexed to appear in search results.'));
		$t->extra = 'id="include_in_search" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->advanced = true;

		$t =& $this->addWidget ('text', 'sort_weight');
		$t->alt = intl_get ('Sorting Weight');
		$help = addslashes (intl_get ('This determines the position of the page within the web site menus and site maps.  Pages with a higher value appear closer to the top.  Pages with the same value are sorted alphabetically.'));
		$t->extra = 'id="sort_weight" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->advanced = true;

		$t =& $this->addWidget ('select', 'is_section');
		$t->alt = intl_get ('Is This a Section Index?');
		$t->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$t->setValue ('no');
		$help = addslashes (intl_get ('If you make this page a section index, then pages below it will adopt its template settings if theirs is not specified explicitly.  This allows you to give a consistent look and feel to entire sections of your web site.'));
		$t->extra = 'id="is_section" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->advanced = true;

		$t =& $this->addWidget ('xed.Widget.Linker', 'external');
		$t->alt = intl_get ('Forward to (URL)');
		$help = addslashes (intl_get ('If you provide a link to an external web page or file (ie. a PDF or Word document), then this page can act as an alias for that resource within your web site navigation.'));
		$t->extra = 'size="40" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->length = 128;
		$t->advanced = true;
		$t->files = false;
		$t->anchors = false;
		$t->email = false;

		$t =& $this->addWidget ('section', 'section3');
		$t->title = intl_get ('Page Attributes');

		$t =& $this->addWidget ('cms.Widget.Keywords', 'keywords');
		$t->alt = intl_get ('Keywords');
		$help = addslashes (intl_get ('Type in or select the keywords from the global list that describe the current page.  Keywords help target your page to its intended audience in search engines and site searches.'));
		$t->extra = 'id="keywords" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('textarea', 'description');
		$t->alt = intl_get ('Description');
		$t->rows = 3;
		$t->labelPosition = 'left';
		$help = addslashes (intl_get ('A description helps target your page to its intended audience in search engines and site searches performed by visitors.'));
		$t->extra = 'id="description" style="overflow: hidden" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$w =& $this->addWidget ('tab', 'tab3');
		$w->title = intl_get ('State');

		// state widgets go here

		$t =& $this->addWidget ('status', 'sitellite_status');
		$t->collection = 'sitellite_page';
		$t->alt = intl_get ('Status');
		$t->setValue ('draft');
		$help = addslashes (intl_get ('The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the scheduler.'));
		$t->extra = 'id="sitellite_status" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('access', 'sitellite_access');
		$t->alt = intl_get ('Access Level');
		$t->setValue ('public');
		$help = addslashes (intl_get ('The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).'));
		$t->extra = 'id="sitellite_access" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('calendar', 'sitellite_startdate');
		$t->alt = intl_get ('Publish On (If Status is "Queued")');
		$t->nullable = true;
		$t->showsTime = true;
		$t->displayFormat = 'datetime';
		$t->advanced = true;

		$t =& $this->addWidget ('calendar', 'sitellite_expirydate');
		$t->alt = intl_get ('Archive On (If Status is "Approved")');
		$t->nullable = true;
		$t->showsTime = true;
		$t->displayFormat = 'datetime';
		$t->advanced = true;

		$t =& $this->addWidget ('owner', 'sitellite_owner');
		$t->alt = intl_get ('Created By');
		$t->setValue (session_username ());
		$t->advanced = true;

		$t =& $this->addWidget ('team', 'sitellite_team');
		$t->alt = intl_get ('Owned by Team');
		$t->setValue (session_team ());
		$t->extra = 'id="sitellite_team"';
		$t->advanced = true;

		$t =& $this->addWidget ('textarea', 'changelog');
		$t->alt = intl_get ('Change Summary');
		$t->rows = 3;
		$t->labelPosition = 'left';
		$help = addslashes (intl_get ('The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this page.'));
		$t->extra = 'id="changelog" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->setValue ('Page added.');

		$w =& $this->addWidget ('tab', 'tab-end');

		// submit buttons
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Create'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		$b =& $w->addButton ('submit_button', intl_get ('Save and continue'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		$b =& $w->addButton ('submit_button', intl_get ('Preview'));
		$b->extra = 'onclick="return cms_preview (this.form)"';

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';

		$this->error_mode = 'all';

		if (! empty ($cgi->_dupe)) {
			loader_import ('cms.Versioning.Rex');
			$rex = new Rex ($cgi->collection); // default: database, database
			$_document = $rex->getCurrent ($cgi->_dupe);
			unset ($cgi->_dupe);

			foreach ((array) $_document as $k => $v) {
				if (in_array ($k, array ('id', 'below_page', 'is_section', 'sitellite_status', 'sitellite_access', 'sitellite_startdate', 'sitellite_expirydate', 'sitellite_owner', 'sitellite_team', 'sort_weight'))) {
					continue;
				}
				if (isset ($this->widgets[$k])) {
					$this->widgets[$k]->setValue ($v);
				}
			}
		}
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		$rex = new Rex ($collection); // default: database, database

		//$vals['sitellite_owner'] = session_username ();
		//$vals['sitellite_team'] = session_team ();
                $continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab-end']);
		unset ($vals['section1']);
		unset ($vals['section3']);

		$res = $rex->create ($vals, $changelog);

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $return) {
			$return = site_prefix () . '/index/' . $vals['id'];
		}

		if (! $res) {
			echo loader_box ('cms/error', array (
				'message' => $rex->error,
				'collection' => $collection,
				'key' => $key,
				'action' => $method,
				'data' => $vals,
				'changelog' => $changelog,
				'return' => $return,
			));
		} else {
			loader_import ('cms.Workflow');
			echo Workflow::trigger (
				'add',
				array (
					'collection' => $collection,
					'key' => $key,
					'data' => $vals,
					'changelog' => $changelog,
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			session_set ('sitellite_alert', intl_get ('Your item has been created.'));

                        if ($continue) {
                                header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
                                exit;
                        }

			if ($return) {
				header ('Location: ' . $return);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/' . $vals['id']);
			exit;
		}
	}
}

function cms_rule_no_actions ($vals) {
	if (preg_match ('/-(app|action|form)$/i', $vals['id'])) {
		return false;
	}
	return true;
}

echo CMS_JS_PREVIEW;
echo CMS_JS_CANCEL;

echo loader_box ('cms/user/preferences/level');

$form = new CmsAddSitellite_pageForm;
echo $form->run ();

?>
