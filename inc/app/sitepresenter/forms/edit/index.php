<?php

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

class SitepresenterEditForm extends MailForm {
	function SitepresenterEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitepresenter/forms/edit/settings.php');

		global $page, $cgi;

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		page_onload ('cms_init_edit_panels ()');
		page_add_script ('
			function cms_cancel_unlock (f, collection, key) {
				if (arguments.length == 0) {
					window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/cms-app";
				} else {
					if (f.elements[\'_return\'] && f.elements[\'_return\'].value.length > 0) {
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + f.elements[\'_return\'].value;
					} else {
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/sitepresenter-app";
					}
				}
				return false;
			}
		');

		// add cancel handler
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return cms_cancel_unlock (this.form, \'' . $cgi->_collection . '\', \'' . $cgi->_key . '\')"';

		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection);
		$_document = $rex->getCurrent ($cgi->_key);

		page_title (intl_get ('Editing Presentation') . ': ' . $_document->title);

		// set values from repository entry
		foreach (get_object_vars ($_document) as $k => $v) {
			if (is_object ($this->widgets[$k])) {
				$this->widgets[$k]->setValue ($v);
			}
		}
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

		$rex = new Rex ($collection);

		unset ($vals['submit_button']);
		unset ($vals['edit-top']);
		unset ($vals['edit-middle']);
		unset ($vals['edit-middle2']);
		unset ($vals['edit-middle3']);
		unset ($vals['edit-bottom']);
		unset ($vals['cover_heading']);

		$method = $rex->determineAction ($key, $vals['sitellite_status']);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		// remove lock when editing is finished
		lock_remove ($collection, $key);

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
			header ('Location: ' . site_prefix () . '/index/sitepresenter-slides-action/id.' . $key);
			exit;
		}
	}
}

?>