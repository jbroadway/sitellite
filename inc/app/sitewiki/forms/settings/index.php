<?php

page_title (intl_get ('Wiki Settings'));

if (! is_writeable ('inc/app/sitewiki/conf/settings.php')) {
	echo '<p style="color: #c00; font-weight: bold">Warning: The inc/app/sitewiki/conf/settings.php file is not writeable by the web server user.</p>';
	return;
}

class SitewikiSettingsForm extends MailForm {
	function SitewikiSettingsForm () {
		parent::MailForm (__FILE__);
		$settings = ini_parse ('inc/app/sitewiki/conf/settings.php', false);
		foreach ($settings as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		loader_import ('saf.File');

		file_overwrite ('inc/app/sitewiki/conf/settings.php', ini_write ($vals));

		echo '<p>Settings saved.  <a href="' . site_prefix () . '/index/sitewiki-app">Continue</a></p>';
	}
}

?>