<?php

set_time_limit (0);

loader_import ('cms.Versioning.Rex');

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
	$r = new Rex ('sitellite_filesystem');

	// determine new name value
	if (! empty ($vals['name'])) {
		$new = $vals['name'];
	} elseif (is_object ($vals['file'])) {
		$new = $vals['file']->name;
	}

	if (! empty ($vals['folder'])) {
		$new = $vals['folder'] . '/' . $new;
	}

	$new = preg_replace ('|/+|', '/', $new);

	if (strpos ($new, '/') === 0) {
		$new = substr ($new, 1);
	}

	if ($r->getCurrent ($new)) {
		// already exists
		return false;
	}

	// doesn't exist yet
	return true;
}

class FilechooserAddForm extends MailForm {
	function FilechooserAddForm () {
		parent::MailForm (__FILE__);
		page_title (intl_get ('Quick Add'));
	}

	function onSubmit ($vals) {
		$return = $vals['return'];
		$folder = $vals['folder'];
		$vals['body'] =& $vals['file'];

		unset ($vals['file']);
		unset ($vals['folder']);
		unset ($vals['return']);
		unset ($vals['submit_button']);

		$vals['sitellite_owner'] = session_username ();
		$vals['sitellite_team'] = session_team ();
		$vals['sitellite_access'] = 'public';
		$vals['sitellite_status'] = 'approved';
		$vals['name'] = $folder . '/' . $vals['body']->name;

		$vals['name'] = preg_replace ('|/+|', '/', $vals['name']);

		if (strpos ($vals['name'], '/') === 0) {
			$vals['name'] = substr ($vals['name'], 1);
		}

		$vals['display_title'] = '';
		$vals['keywords'] = '';
		$vals['description'] = '';

		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('sitellite_filesystem');

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		$res = $rex->create ($vals, 'File added with Quick Add.');
		if (! $res) {
			echo loader_box ('cms/error', array (
				'message' => $rex->error,
				'collection' => 'sitellite_filesystem',
				'key' => $key,
				'action' => 'create',
				'data' => $vals,
				'changelog' => 'File added with Quick Add.',
				'return' => $return,
			));
			return;
		}

		header ('Location: ' . $return);
		exit;
	}
}

?>