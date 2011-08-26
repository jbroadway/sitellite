<?php

loader_import ('saf.File.Directory');
loader_import ('imagechooser.Functions');

class ImagechooserAdminEditForm extends MailForm {
	function ImagechooserAdminEditForm () {
		parent::MailForm (__FILE__);
		global $cgi;
		page_title (intl_get ('Editing properties') . ': ' . $cgi->location . '/' . $cgi->src);
		$info = pathinfo ($cgi->src);
		$this->widgets['new_name']->setValue ($cgi->src);
		$this->widgets['new_name']->addRule (
			'func "imagechooser_rule_name_invalid"',
			intl_get ('Your filename does not appear to be valid.')
		);
		$this->widgets['new_name']->addRule (
			'func "imagechooser_rule_name_extension"',
			intl_get ('Your filename must end in') . ' .' . strtolower ($info['extension'])
		);
		$this->widgets['new_name']->addRule (
			'func "imagechooser_rule_name_exists"',
			intl_get ('The filename you chose already exists, please choose another.')
		);

		$dirs = Dir::getStruct ('pix');
		$locations = array ('/pix' => '/pix');
		foreach ($dirs as $k => $v) {
			if (strpos ($v, 'CVS') !== false) {
				continue;
			}
			$locations['/' . $v] = '/' . $v;
		}
		$this->widgets['new_location']->setValues ($locations);
		$this->widgets['new_location']->setValue ($cgi->location);
	}

	function onSubmit ($vals) {
		if ($vals['src'] != $vals['new_name'] || $vals['location'] != $vals['new_location']) {
			if (imagechooser_rename ($vals['location'] . '/' . $vals['src'], $vals['new_location'] . '/' . $vals['new_name'])) {
				imagechooser_update_pages ($vals['location'] . '/' . $vals['src'], $vals['new_location'] . '/' . $vals['new_name']);
			}
		}

		header ('Location: ' . site_prefix () . '/index/imagechooser-admin-action?name=&location=' . $vals['new_location'] . '&format=html&attrs=');
		exit;
	}
}

function imagechooser_rule_name_invalid ($vals) {
	if (strpos ($vals['new_name'], '..') !== false || strpos ($vals['new_name'], '/') !== false || strpos ($vals['new_name'], '.') === 0) {
		return false;
	}
	return true;
}

function imagechooser_rule_name_extension ($vals) {
	$info = pathinfo ($vals['src']);
	$new_info = pathinfo ($vals['new_name']);
	if (strtolower ($info['extension']) != strtolower ($new_info['extension'])) {
		return false;
	}
	return true;
}

function imagechooser_rule_name_exists ($vals) {
	if ($vals['src'] != $vals['new_name'] || $vals['location'] != $vals['new_location']) {
		if (@file_exists (ltrim ($vals['new_location'], '/') . '/' . $vals['new_name'])) {
			return false;
		}
	}
	return true;
}

?>