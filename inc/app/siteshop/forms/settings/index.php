<?php

if (! is_writable ('inc/app/siteshop/conf/settings.php')) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Can\'t write to the settings file, please check your server permissions and try again.') . '</p>';
	return;
}

class SiteshopSettingsForm extends MailForm {
	function SiteshopSettingsForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/settings/settings.php');
		page_title (intl_get ('Settings'));
		$conf = ini_parse ('inc/app/siteshop/conf/settings.php');
		foreach ($conf as $section => $settings) {
			if ($section == 'Taxes') {
				$o = '';
				$sep = '';
				foreach ($settings as $k => $v) {
					$o .= $sep . $k . ' ' . $v;
					$sep = "\n";
				}
				$this->widgets['tax_input']->setValue ($o);
			} else {
				foreach ($settings as $k => $v) {
					$this->widgets[$k]->setValue ($v);
				}
			}
		}
	}

	function onSubmit ($vals) {
		$conf = ini_parse ('inc/app/siteshop/conf/settings.php');
		foreach ($conf as $section => $settings) {
			if ($section == 'Taxes') {
				$new_taxes = array ();
				foreach (explode ("\n", $vals['tax_input']) as $line) {
					$line = trim ($line);
					list ($k, $v) = explode (' ', $line, 2);
					$new_taxes[$k] = $v;
				}
				$conf[$section] = $new_taxes;
			} else {
				foreach ($settings as $k => $v) {
					$conf[$section][$k] = $vals[$k];
				}
			}
		}

		$fp = fopen ('inc/app/siteshop/conf/settings.php', 'w');
		fwrite ($fp, ini_write ($conf));
		fclose ($fp);

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-index-action');
		exit;
	}
}

?>