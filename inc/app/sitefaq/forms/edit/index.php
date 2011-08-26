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

class SitefaqEditForm extends MailForm { // default to a simple edit screen, much like myadm
	function SitefaqEditForm () {
		parent::MailForm ();

		global $page, $cgi;

		$this->extra = 'id="cms-edit-form"';

		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection); // default: database, database
		$_document = $rex->getCurrent ($cgi->_key);
		$widgets = $rex->getStruct ();
		if (! $widgets) {
			$widgets = array ();
		}

		// edit widgets go here
		$this->widgets = array_merge ($this->widgets, $widgets);
		foreach ($this->widgets as $k => $v) {
			if (isset ($_document->{$k})) {
				$this->widgets[$k]->setValue ($_document->{$k});
			}
		}

		$w =& $this->addWidget ('hidden', '_key');
		$w =& $this->addWidget ('hidden', '_collection');
		$w =& $this->addWidget ('hidden', '_return');

		if ($rex->isVersioned) {
			$t =& $this->addWidget ('textarea', 'changelog');
			$t->alt = intl_get ('Change Summary');
			$t->rows = 3;
			$t->labelPosition = 'left';
			$t->extra = 'id="changelog"';
		}

		// submit buttons
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';

		$this->error_mode = 'all';

		if ($rex->info['Collection']['singular']) {
			page_title (intl_get ('Editing') . ' ' . $rex->info['Collection']['singular'] . ': ' . $_document->{$rex->key});
		} else {
			page_title (intl_get ('Editing Item') . ': ' . $_document->{$rex->key});
		}

		// the SiteFAQ additions:
		if (appconf ('user_anonymity')) {
			unset ($this->widgets['name']);
			unset ($this->widgets['email']);
			unset ($this->widgets['url']);
			unset ($this->widgets['ip']);
			unset ($this->widgets['member_id']);
		}

		$admin_roles = session_admin_roles ();
		$this->widgets['assigned_to']->setValues (
			db_pairs (
				'select username, concat(lastname, ", ", firstname, " (", username, ")")
				from sitellite_user
				where role in("' . join ('", "', $admin_roles) . '")
				order by lastname, firstname, username'
			)
		);
		if (! $_document->assigned_to) {
			$this->widgets['assigned_to']->setValue (session_username ());
		}
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['_collection'];
		unset ($vals['_collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$rex = new Rex ($collection); // default: database, database

		unset ($vals['submit_button']);

		$key = $vals['_key'];
		unset ($vals['_key']);

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		$method = $rex->determineAction ($key, $vals['sitellite_status']);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		if (! $res) {
			if (empty ($return)) {
				$return = site_prefix () . '/index/sitefaq-app';
			}
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
				'edit',
				array (
					'collection' => $collection,
					'key' => $key,
					'action' => $method,
					'data' => $vals,
					'changelog' => $changelog,
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			session_set ('sitellite_alert', intl_get ('Your item has been saved.'));

			if (! empty ($return)) {
				header ('Location: ' . $return);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/sitefaq-app');
			exit;
		}
	}
}

echo CMS_JS_CANCEL;
echo CMS_JS_PREVIEW;
$form = new SitefaqEditForm;
echo $form->run ();

?>