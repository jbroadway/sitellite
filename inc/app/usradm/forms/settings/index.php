<?php

page_title (intl_get ('Site Settings'));

if (! is_writeable ('inc/conf/config.ini.php')) {
	echo '<p class="invalid">' . intl_get ('Warning: The configuration file is not writeable.  Please verify that the file \'inc/conf/config.ini.php\' is writeable by the web server user.') . '</p>';
	return;
}

class UsradmSettingsForm extends MailForm {
	function UsradmSettingsForm () {
		parent::MailForm (__FILE__);

		page_add_style ('
			td.label {
				width: 200px;
			}
			td.field {
				padding-left: 7px;
				padding-right: 7px;
			}
		');

		$config = ini_parse ('inc/conf/config.ini.php');
		foreach ($config as $cname => $conf) {
			foreach ($conf as $k => $v) {
				if (isset ($this->widgets[$cname . '_' . $k])) {
					$this->widgets[$cname . '_' . $k]->setValue ($v);
				}
			}
		}
	}

	function onSubmit ($vals) {
		unset ($vals['Database']);
		unset ($vals['Site']);
		unset ($vals['Server']);
		unset ($vals['I18n']);
        unset ($vals['Emailing']);
		unset ($vals['Messaging']);
		unset ($vals['Services']);
		unset ($vals['submit_button']);

		$config = array ();
		foreach ($vals as $k => $v) {
			list ($cname, $key) = explode ('_', $k, 2);
			if (! isset ($config[$cname])) {
				$config[$cname] = array ($key => $v);
			} else {
				$config[$cname][$key] = $v;
			}
		}

		loader_import ('saf.File');
		if (! file_overwrite ('inc/conf/config.ini.php', ini_write ($config))) {
			die ('Error writing to file: inc/conf/config.ini.php');
		}

		session_set ('sitellite_alert', intl_get ('Site settings saved.'));
		header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
		exit;
	}
}

?>
