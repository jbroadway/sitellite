<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// Menu is a class that is used to generate navigation systems on the
// fly on web sites, based on a self-referencial database table structure.
// It is mostly for use through EasyText.
//
// resolved tickets:
// #174 CMS cancel.
//

set_time_limit (0);

function sitellite_filesystem_rule_extension ($vals) {
	if (! empty ($vals['name'])) {
		$name = $vals['name'];
	} elseif (is_uploaded_file ($vals['file']->tmp_name)) {
		$name = $vals['file']->name;
	}

	if (isset ($name) && ! preg_match ('|\.[a-zA-Z0-9_-]+$|', $name)) {
		return false;
	}

	return true;
}

function sitellite_filesystem_rule_unique ($vals) {
	$orig = $vals['_key'];

	// determine new name value
	if (! empty ($vals['name'])) {
		$new = $vals['name'];
	} elseif (is_object ($vals['file'])) {
		$new = $vals['file']->name;
	}

	if (! empty ($vals['folder'])) {
		$new = $vals['folder'] . '/' . $new;
	}

	if (strpos ($new, '/') === 0) {
		$new = substr ($new, 1);
	}

	if ($orig == $new) {
		// ID unchanged
		return true;
	}

	$r = new Rex ($vals['_collection']);

	if ($r->getCurrent ($new)) {
		// already exists
		return false;
	}

	// doesn't exist yet
	return true;
}

global $cgi;

loader_import ('cms.Workflow.Lock');

lock_init ();

if (lock_exists ($cgi->_collection, $cgi->_key)) {
	page_title (intl_get ('Item Locked by Another User'));
	echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
	echo template_simple (LOCK_INFO_TEMPLATE, lock_info ($cgi->_collection, $cgi->_key));
	return;
} else {
	lock_add ($cgi->_collection, $cgi->_key);
}

class CmsEditSitellite_filesystemForm extends MailForm {
	function CmsEditSitellite_filesystemForm () {
		parent::MailForm ();

		$this->autosave = true;

		$this->parseSettings ('inc/app/cms/forms/edit/sitellite_filesystem/settings.php');

		global $page, $cgi;

		page_title (intl_get ('Editing File') . ': ' . $cgi->_key);

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		page_add_script ('
			function cms_cancel_unlock (f, collection, key) {
				onbeforeunload_form_submitted = true;
				if (arguments.length == 0) {
					window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/cms-app";
				} else {
					if (f.elements[\'_return\'] && f.elements[\'_return\'].value.length > 0) {
// Start: SEMIAS #174 CMS cancel.
// ----------------------- 
// window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + EncodeURIComponent (f.elements[\'_return\'].value);
// -----------------------
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + encodeURIComponent (f.elements[\'_return\'].value);
// END: SEMIAS
					} else {
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/cms-app";
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

		// add cancel handler
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="return cms_cancel_unlock (this.form, \'' . $cgi->_collection . '\', \'' . $cgi->_key . '\')"';

		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection);
		$_document = $rex->getCurrent ($cgi->_key);

		// set values from repository entry
		$info = pathinfo ($_document->name);
		if ($info['dirname'] == '.') {
			$info['dirname'] = '';
		}
		$_document->folder = $info['dirname'];
		$_document->file = $_document->name;
		unset ($_document->name);

		foreach (get_object_vars ($_document) as $k => $v) {
			if (is_object ($this->widgets[$k])) {
				$this->widgets[$k]->setValue ($v);
			}
		}

		/*if ($rex->determineAction ($cgi->_key) != 'modify') {
			// turn name field into hidden w/ _key's value
			$this->widgets['name'] =& $this->widgets['name']->changeType ('info');
			$this->widgets['name']->setValue (basename ($cgi->_key));
			$this->widgets['folder'] =& $this->widgets['folder']->changeType ('info');
			$this->widgets['folder']->setValue (dirname ($cgi->_key));
		}*/
	}

	function show () {
		return loader_box ('cms/user/preferences/level') . parent::show ();
	}

	function isNewFolder ($folder, $key) {
		$info = pathinfo ($key);
		if ($folder != $info['dirname']) {
			return true;
		}
		return false;
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['_collection'];
		unset ($vals['_collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$key = $vals['_key'];
		unset ($vals['_key']);

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		if (is_object ($vals['file'])) {
			$vals['body'] =& $vals['file'];
			unset ($vals['file']);
		} else {
			unset ($vals['file']);
		}

		if (! empty ($vals['name'])) {
			$vals['name'] = $vals['folder'] . '/' . $vals['name'];
		} elseif ($this->isNewFolder ($vals['folder'], $key)) {
			$vals['name'] = $vals['folder'] . '/' . basename ($key);
		} elseif (is_object ($vals['body'])) {
			$vals['name'] = $vals['folder'] . '/' . $vals['body']->name;
		} else {
			unset ($vals['name']);
		}
		if (strpos ($vals['name'], '/') === 0) {
			$vals['name'] = substr ($vals['name'], 1);
		}
		unset ($vals['folder']);

		$rex = new Rex ($collection);

                $continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab-end']);

		$method = $rex->determineAction ($key, $vals['sitellite_status']);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		// remove lock when editing is finished
		lock_remove ($collection, $key);

		if (empty ($return)) {
			$return = site_prefix () . '/index/cms-browse-action?collection=sitellite_filesystem';
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
			header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=sitellite_filesystem');
			exit;
		}
	}
}

?>
