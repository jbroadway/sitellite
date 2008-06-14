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

$rex = new Rex ($cgi->collection);

session_set ('imagechooser_path', '/pix');

if (! $rex->collection) {
	page_title (intl_get ('Error: Collection not found!'));
	echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">' . intl_get ('Back') . '</a></p>';
	return;
}

if (! session_allowed ('add', 'rw', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

if (session_is_resource ($cgi->collection) && ! session_allowed ($cgi->collection, 'r', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

if (isset ($rex->info['Collection']['add'])) {
	list ($call, $name) = explode (':', $rex->info['Collection']['add']);
	if ($call == 'box') {
		echo loader_box ($name);
	} elseif ($call == 'form') {
		echo loader_form ($name);
	} else {
		echo loader_form ($call);
	}
	return;

} else {

class CmsAddForm extends MailForm {
	function CmsAddForm () {
		parent::MailForm ();

		$w =& $this->addWidget ('hidden', 'collection');
		$w =& $this->addWidget ('hidden', '_return');

		global $cgi;
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ($cgi->collection);
		$widgets = $rex->getStruct ();
		if (! $widgets) {
			die ($rex->error);
		}

		$this->widgets = array_merge ($this->widgets, $widgets);

		foreach (array_keys ($this->widgets) as $k) {
			if (strtolower (get_class ($this->widgets[$k])) == 'mf_widget_xeditor') {
				$this->extra = 'onsubmit="xed_copy_value (this, \'' . $k . '\')"';
			}
		}

		if (isset ($rex->info['Collection']['singular'])) {
			page_title (intl_get ('Adding') . ' ' . $rex->info['Collection']['singular']);
		} else {
			page_title (intl_get ('Adding Item'));
		}

		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Create'));

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';
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

		$rex = new Rex ($collection); // default: database, database

		unset ($vals['submit_button']);

		$res = $rex->create ($vals);

		if (isset ($vals[$rex->key]) && $vals[$rex->key] != false) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $res) {
			die ($rex->error);
		} else {
			loader_import ('cms.Workflow');
			echo Workflow::trigger (
				'add',
				array (
					'collection' => $collection,
					'key' => $key,
					'data' => $vals,
					'changelog' => intl_get ('Item added.'),
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=' . urlencode ($collection));
			exit;
		}

	}
}

//echo CMS_JS_PREVIEW;
echo CMS_JS_CANCEL;

$form = new CmsAddForm;
echo $form->run ();

} // end else

?>