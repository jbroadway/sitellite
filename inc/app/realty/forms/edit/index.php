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

class CmsEditForm extends MailForm { // default to a simple edit screen, much like myadm
	function CmsEditForm () {
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
			if (strtolower (get_class ($this->widgets[$k])) == 'mf_widget_xeditor') {
				$this->extra = 'onsubmit="xed_copy_value (this, \'' . $k . '\')"';
			}
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

		global $cgi;
		if (! is_object ($cgi->photo1)) {
			if ($cgi->photo1_clear == 'no') {
				unset ($vals['photo1']);
			}
		} else {
			$cgi->photo1->move ('inc/app/realty/pix', $vals['_key'] . 'a.jpg');
			$vals['photo1'] = '/inc/app/realty/pix/' . $vals['_key'] . 'a.jpg';
		}
		if (! is_object ($cgi->photo2)) {
			if ($cgi->photo2_clear == 'no') {
				unset ($vals['photo2']);
			}
		} else {
			$cgi->photo2->move ('inc/app/realty/pix', $vals['_key'] . 'b.jpg');
			$vals['photo2'] = '/inc/app/realty/pix/' . $vals['_key'] . 'b.jpg';
		}
		if (! is_object ($cgi->photo3)) {
			if ($cgi->photo3_clear == 'no') {
				unset ($vals['photo3']);
			}
		} else {
			$cgi->photo3->move ('inc/app/realty/pix', $vals['_key'] . 'c.jpg');
			$vals['photo3'] = '/inc/app/realty/pix/' . $vals['_key'] . 'c.jpg';
		}
		if (! is_object ($cgi->photo4)) {
			if ($cgi->photo4_clear == 'no') {
				unset ($vals['photo4']);
			}
		} else {
			$cgi->photo4->move ('inc/app/realty/pix', $vals['_key'] . 'd.jpg');
			$vals['photo4'] = '/inc/app/realty/pix/' . $vals['_key'] . 'd.jpg';
		}
		if (! is_object ($cgi->photo5)) {
			if ($cgi->photo5_clear == 'no') {
				unset ($vals['photo5']);
			}
		} else {
			$cgi->photo5->move ('inc/app/realty/pix', $vals['_key'] . 'e.jpg');
			$vals['photo5'] = '/inc/app/realty/pix/' . $vals['_key'] . 'e.jpg';
		}
		if (! is_object ($cgi->photo6)) {
			if ($cgi->photo6_clear == 'no') {
				unset ($vals['photo6']);
			}
		} else {
			$cgi->photo6->move ('inc/app/realty/pix', $vals['_key'] . 'f.jpg');
			$vals['photo6'] = '/inc/app/realty/pix/' . $vals['_key'] . 'f.jpg';
		}
		if (! is_object ($cgi->photo7)) {
			if ($cgi->photo7_clear == 'no') {
				unset ($vals['photo7']);
			}
		} else {
			$cgi->photo7->move ('inc/app/realty/pix', $vals['_key'] . 'g.jpg');
			$vals['photo7'] = '/inc/app/realty/pix/' . $vals['_key'] . 'g.jpg';
		}
		if (! is_object ($cgi->photo8)) {
			if ($cgi->photo8_clear == 'no') {
				unset ($vals['photo8']);
			}
		} else {
			$cgi->photo8->move ('inc/app/realty/pix', $vals['_key'] . 'h.jpg');
			$vals['photo8'] = '/inc/app/realty/pix/' . $vals['_key'] . 'h.jpg';
		}

		$rex = new Rex ($collection); // default: database, database

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

		$method = $rex->determineAction ($key, $vals['sitellite_status']);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		if (! $res) {
			die ($rex->error);
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

			if (! empty ($return)) {
				header ('Location: ' . $return);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/realty-details-action/id.' . $key);
			exit;
		}
	}
}

echo CMS_JS_CANCEL;
echo CMS_JS_PREVIEW;
$form = new CmsEditForm;
echo $form->run ();

?>