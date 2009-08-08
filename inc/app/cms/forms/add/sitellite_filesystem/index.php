<?php

set_time_limit (0);

function sitellite_filesystem_rule_extension ($vals) {
	foreach ($vals['file'] as $k=>$f) {
		if (! empty ($vals['name'])) {
			if ($k) {
				$name = $k . '.' . $vals['name'];
			}
			else {
				$name = $vals['name'];
			}
		} elseif (is_uploaded_file ($vals['file'][$k]->tmp_name)) {
			$name = $vals['file']->name;
		}

		if (isset ($name) && ! preg_match ('|\.[a-zA-Z0-9_-]+$|', $name)) {
			return false;
		}
	}

	return true;
}

function sitellite_filesystem_rule_unique ($vals) {
	$r = new Rex ($vals['collection']);

	foreach ($vals['file'] as $k=>$f) {
		// determine new name value
		if (! empty ($vals['name'])) {
			if ($k) {
				$new = $k . '.' . $vals['name'];
			}
			else {
				$new = $vals['name'];
			}
		} elseif (is_object ($f)) {
			$new = $f->name;
		}

		if (! empty ($vals['folder'])) {
			$new = $vals['folder'] . '/' . $new;
		}

		if (strpos ($new, '/') === 0) {
			$new = substr ($new, 1);
		}

		if ($r->getCurrent ($new)) {
			// already exists
			return false;
		}
	}

	// doesn't exist yet
	return true;
}

class CmsAddSitellite_filesystemForm extends MailForm {
	function CmsAddSitellite_filesystemForm () {
		parent::MailForm ();

		global $page, $cgi;

		$this->parseSettings ('inc/app/cms/forms/add/sitellite_filesystem/settings.php');

		page_title (intl_get ('Adding File'));

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		page_add_script ('
			function cms_cancel (f) {
				onbeforeunload_form_submitted = true;
				if (arguments.length == 0) {
					window.location.href = "/index/cms-browse-action?collection=sitellite_filesystem";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/cms-browse-action?collection=sitellite_filesystem";
					}
				}
				return false;
			}

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
	
		$this->widgets['sitellite_owner']->setValue (session_username ());
		$this->widgets['sitellite_team']->setValue (session_team ());

		// add cancel handler
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="return cms_cancel (this.form)"';
	}

	function show () {
		return loader_box ('cms/user/preferences/level') . parent::show ();
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		$files =& $vals['file'];
		unset ($vals['file']);
		$name = $vals['name'];
		$folder = $vals['folder'];
		unset ($vals['folder']);

		$rex = new Rex ('sitellite_filesystem');

		$continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab-end']);

		foreach ($files as $k=>$file) {

			$vals['body'] = $file;
			if (! empty ($name)) {
				if ($k) {
					$vals['name'] = $folder . '/' . $k . '.' . $name;
				}
				else {
					$vals['name'] = $folder . '/' . $name;
				}
			} else {
				$vals['name'] = $folder . '/' . $vals['body']->name;
			}
			if (strpos ($vals['name'], '/') === 0) {
				$vals['name'] = substr ($vals['name'], 1);
			}

			$res = $rex->create ($vals, $changelog);

			if (isset ($vals[$rex->key])) {
				$key = $vals[$rex->key];
			} elseif (! is_bool ($res)) {
				$key = $res;
			} else {
				$key = 'Unknown';
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
			}
		}

		session_set ('sitellite_alert', intl_get ('Your item has been created.'));

		if ($continue) {
			header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
			exit;
		}

		if (! empty ($return)) {
			header ('Location: ' . $return);
			exit;
		}
		header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=sitellite_filesystem');
		exit;
	}
}

?>
