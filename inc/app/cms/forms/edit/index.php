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

global $cgi;

loader_import ('cms.Versioning.Rex');

$rex = new Rex ($cgi->_collection);

session_set ('imagechooser_path', '/pix');

if (! $rex->collection) {
	page_title (intl_get ('Error: Collection not found!'));
	echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">' . intl_get ('Back') . '</a></p>';
	return;
}

if (session_is_resource ($cgi->_collection) && ! session_allowed ($cgi->_collection, 'r', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

if (isset ($rex->info['Collection']['edit'])) {
	list ($call, $name) = explode (':', $rex->info['Collection']['edit']);
	if ($call == 'box') {
		echo loader_box ($name);
	} elseif ($call == 'form') {
		echo loader_form ($name);
	} else {
		echo loader_form ($call);
	}
	return;

} else {

class CmsEditForm extends MailForm { // default to a simple edit screen, much like myadm
	function CmsEditForm () {
		parent::MailForm ();

		$this->autosave = true;

		global $page, $cgi;

		$this->extra = 'id="cms-edit-form"';

		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection); // default: database, database
		if (strpos ($rex->key, ',') !== false) {
			$pkeys = preg_split ('/, ?/', $rex->key);
			$pvals = explode ('|', $cgi->_key);
			$key = array ();
			for ($i = 0; $i < count ($pkeys); $i++) {
				$key[$pkeys[$i]] = $pvals[$i];
			}
		} else {
			$key = $cgi->_key;
		}
		$_document = $rex->getCurrent ($key);
		$widgets = $rex->getStruct ();
		if (! $widgets) {
			$widgets = array ();
		}

		// edit widgets go here
		$this->widgets = array_merge ($this->widgets, $widgets);
		foreach ($this->widgets as $k => $v) {
			if (strtolower (get_class ($this->widgets[$k])) == 'mf_widget_xeditor') {
				$this->extra = 'onsubmit="xed_copy_value (this, \'' . $k . '\')"';
			}
			if (isset ($_document->{$k})) {
				$this->widgets[$k]->setValue ($_document->{$k});
			}
		}

		foreach ($rex->info as $k => $v) {
			if (preg_match ('/^hint:(.*)$/', $k, $regs)) {
				if (! isset ($this->widgets[$regs[1]])) {
					$w =& $this->createWidget ($regs[1], $v);
					$w->id = $cgi->_key;
				}
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
			if (isset ($rex->info['hint:changelog'])) {
				$hint =& $rex->info['hint:changelog'];
				if ($hint['type']) {
					$t =& $t->changeType ($hint['type']);
					unset ($hint['type']);
				}
				foreach ($hint as $k => $v) {
					if (method_exists ($t, $k)) {
						$t->{$k} ($v);
					} else {
						$t->{$k} = $v;
					}
				}
			}
			$this->widgets['changelog'] =& $t;
		}

		// submit buttons
		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		$b =& $w->addButton ('submit_button', intl_get ('Save and continue'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';

		$this->error_mode = 'all';

		if ($rex->info['Collection']['singular']) {
			page_title (intl_get ('Editing') . ' ' . $rex->info['Collection']['singular'] . ': ' . $_document->{$rex->key});
		} else {
			page_title (intl_get ('Editing Item') . ': ' . $_document->{$rex->key});
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

                $continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);

		$key = $vals['_key'];
		unset ($vals['_key']);

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		foreach ($vals as $k => $v) {
			if ($this->widgets[$k]->ignoreEmpty && empty ($v)) {
				unset ($vals[$k]);
			}
		}

		if (strpos ($rex->key, ',') !== false) {
			$pkeys = preg_split ('/, ?/', $rex->key);
			$pvals = explode ('|', $key);
			$key = array ();
			for ($i = 0; $i < count ($pkeys); $i++) {
				$key[$pkeys[$i]] = $pvals[$i];
			}
		}

		$method = $rex->determineAction ($key, $vals['sitellite_status']);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		if (! $res) {
			if (empty ($return)) {
				$return = site_prefix () . '/index/cms-browse-action?collection=' . urlencode ($collection);
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

                        if ($continue) {
                                header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
                                exit;
                        }

			if (! empty ($return)) {
				header ('Location: ' . $return);
				exit;
			}
			if ($collection == 'sitellite_page') {
				header ('Location: ' . site_prefix () . '/index/' . $key);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=' . urlencode ($collection));
			exit;
		}
	}
}

echo CMS_JS_CANCEL;
echo CMS_JS_PREVIEW;
$form = new CmsEditForm;
echo $form->run ();

} // end else

?>
