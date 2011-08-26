<?php

global $cgi;

@touch ('inc/html/' . $cgi->set . '/config.ini.php');
if (! @is_writable('inc/html/' . $cgi->set) || ! @is_writable('inc/html/' . $cgi->set . '/config.ini.php')) {
	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ('Your template is not writeable by Sitellite.  Please adjust your server settings to continue.') . '</p>';
	return;
}

loader_import('saf.File');
loader_import('sitetemplate.Filters');

class SitetemplateEditsetForm extends MailForm {
	function SitetemplateEditsetForm () {
		parent::MailForm ();
		
		global $cgi;
		
		$set = $cgi->set;

		$this->parseSettings ('inc/app/sitetemplate/forms/editset/settings.php');
		
		$settings = array();
		
		if (file_exists ('inc/html/' . $set . '/config.ini.php')) {
			$settings = ini_parse('inc/html/' . $set . '/config.ini.php', false);
		}

		$name = $settings['set_name'];
		if (! $name) {
			$name = $set;
		}
		
		$settings['set'] = $set;
		
		//put form values into respective forms
		foreach ($settings as $k => $v) {
			$this->widgets[$k]->setDefault ($v);
		}
		
		if (@file_exists ('inc/html/' . $set . '/modes.php')) {
			$this->widgets['modes']->setDefault (@join ('', @file ('inc/html/' . $set . '/modes.php')));
		} else {
			$this->widgets['modes']->setDefault ($modesd);
		}

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="history.go (-1); return false"';

		page_title (intl_get ('Editing Properties') . ': ' . $name);
	}

	function onSubmit ($vals) {
		$set = $vals['set'];
		unset ($vals['set']);
		file_overwrite ('inc/html/' . $set . '/modes.php', $vals['modes']);
		unset ($vals['submit_button']);
		unset ($vals['modes']);
		$r = ini_write($vals);
		file_overwrite ('inc/html/' . $set . '/config.ini.php', $r);
		header ('Location: ' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $set);
		exit;
	}
}

?>
