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

class CmsAddSitellite_sidebarForm extends MailForm {
	function CmsAddSitellite_sidebarForm () {
		parent::MailForm ();

		page_title (intl_get ('Adding Sidebar'));

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
		');
		if (session_pref ('form_help') == 'off') {
			page_add_script ('
				formhelp_disable = true;
			');
		}

		$w =& $this->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Edit');

		// edit widgets go here

		$w =& $this->addWidget ('text', 'id');
		$w->alt = intl_get ('Sidebar ID');
		$w->addRule ('not empty', 'You must enter an ID for your sidebar.');
		$w->addRule ('not regex "[^a-zA-Z0-9_-]"', 'Your ID contains invalid characters.');
		$w->addRule ('unique "sitellite_sidebar/id"', 'A sidebar with this ID already exists.');
		$help = addslashes (intl_get ('Must contain only letters, numbers, underscores, and dashes (ie. product_info).'));
		$w->extra = 'onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$w->length = 32;

		$w =& $this->addWidget ('hidden', 'collection');
		$w =& $this->addWidget ('hidden', '_return');

		$w =& $this->addWidget ('text', 'title');
		$w->alt = intl_get ('Sidebar Title');
		//$w->addRule ('not empty', 'You must enter a title for your sidebar.');
		$w->extra = 'size="40"';
		$w->length = 72;

		$w =& $this->addWidget ('xed.Widget.Xeditor', 'body');
		if (appconf ('tidy_path')) {
			$w->tidy_path = appconf ('tidy_path');
		}
		//$w->addRule ('not empty', 'You must enter content into your sidebar body.');
		$w->length = 65535;

		$w =& $this->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Properties');

		// property widgets go here

		//$t =& $this->addWidget ('section', 'section1');
		//$t->title = intl_get ('Display Settings');

		$res = db_fetch ('select * from sitellite_sidebar_position order by id asc');
		if (! $res) {
			$res = array ('left' => intl_get ('Left'));
		} elseif (is_object ($res)) {
			$res = array ($res->id => ucwords ($res->id));
		} else {
			$n = array ();
			foreach ($res as $row) {
				$n[$row->id] = ucwords ($row->id);
			}
			$res = $n;
		}

		$t =& $this->addWidget ('cms.Widget.Position', 'position');
		$t->alt = intl_get ('Position');
		$t->setValues ($res);
		$help = addslashes (intl_get ('Choose the position where you want this sidebar to appear.'));
		$t->extra = 'id="position" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';

		$t =& $this->addWidget ('text', 'sorting_weight');
		$t->alt = intl_get ('Sorting Order');
		global $cgi;
		if ($cgi->position) {
			$t->setDefault ((string) db_shift ('select sorting_weight + 1 from sitellite_sidebar where position = ? order by sorting_weight desc limit 1', $cgi->position));
		} else {
			$t->setDefault ((string) db_shift ('select sorting_weight + 1 from sitellite_sidebar order by sorting_weight desc limit 1'));
		}
		$help = addslashes (intl_get ('The larger the number, the further down the list of sidebars in the same position the current sidebar will appear.'));
		$t->extra = 'id="sorting_weight" style="width: 50px" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->advanced = true;

		loader_box ('sitellite/nav/init');
		$t =& $this->addWidget ('multiple', 'show_on_pages');
		$t->alt = intl_get ('Show in Sections');
		$t->setValues (
			array_merge (
				array ('all' => intl_get ('All')),
				menu_get_sections ()
			)
		);
		$help = addslashes (intl_get ('Choose the web site sections where you want this sidebar to appear. Ctrl-click to select more than one section.'));
		$t->extra = 'id="show_on_pages" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->size = 5;

		$t =& $this->addWidget ('boxchooser.Widget.Boxchooser', 'alias');
		$t->alt = intl_get ('Alias of (a box name)');
		$help = addslashes (intl_get ('This allows you to specify the name of a box that will provide the contents of this sidebar.'));
		$t->extra = 'id="alias" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->length = 255;
		$t->advanced = true;

		$w =& $this->addWidget ('tab', 'tab3');
		$w->title = intl_get ('State');

		// state widgets go here

		$t =& $this->addWidget ('status', 'sitellite_status');
		$t->alt = intl_get ('Status');
		$t->setValue ('draft');
		$help = addslashes (intl_get ('The status determines what stage of its lifecycle that your document is in.  Only Approved items can be viewed on the live site.  Queued items are set to be approved on the specified Publish On date (below) by the scheduler.'));
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
		$help = addslashes (intl_get ('The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this item.'));
		$t->extra = 'id="changelog" onfocus="formhelp_show (this, \''.$help.'\')" onblur="formhelp_hide ()"';
		$t->setValue ('Sidebar added.');

		$w =& $this->addWidget ('tab', 'tab-end');

		// submit buttons
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Create'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true;"';

		$b =& $w->addButton ('submit_button', intl_get ('Save and continue'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		//$b =& $w->addButton ('submit_button', intl_get ('Preview'));
		//$b->extra = 'onclick="return cms_preview (this.form)"';

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';

		$this->error_mode = 'all';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_sidebar';
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
		//unset ($vals['section1']);
		unset ($vals['section3']);

		if (! $vals['show_on_pages']) {
			$vals['show_on_pages'] = '';
		}

		$res = $rex->create ($vals, $changelog);

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (empty ($return)) {
			$return = site_prefix () . '/index/cms-browse-action?collection=sitellite_sidebar';
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

			if (! empty ($return)) {
				header ('Location: ' . $return);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=sitellite_sidebar');
			exit;
		}
	}
}

echo CMS_JS_PREVIEW;
echo CMS_JS_CANCEL;

echo loader_box ('cms/user/preferences/level');

$form = new CmsAddSitellite_sidebarForm;
echo $form->run ();

?>
