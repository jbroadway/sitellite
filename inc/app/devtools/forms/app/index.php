<?php

if (! @is_writeable ('inc/app')) {
	page_title (intl_get ('An Error Occurred'));
	echo template_simple ('error.spt', array ('msg' => 'Your inc/app folder is not writeable by Sitellite.'));
	return;
}

loader_import ('devtools.Functions');

class DevtoolsAppForm extends MailForm {
	function DevtoolsAppForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/devtools/forms/app/settings.php');

		page_title (intl_get ('Create a New App'));

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/devtools-app\'; return false"';
	}

	function onSubmit ($vals) {
		// your handler code goes here

		chdir ('inc/app');
		$out = shell_exec ('./devtools/bin/app.sh ' . escapeshellarg ($vals['appname']));
		chdir ('../..');

		if (trim ($out) == 'Your app is ready, sir.') {
			chdir ('inc/app');
			shell_exec ('umask 0000; chmod -R 0777 ' . escapeshellarg ($vals['appname']));
			chdir ('../..');

			page_title (intl_get ('App Created'));
			echo template_simple ('app_created.spt', $vals);
		} else {
			page_title (intl_get ('An Error Occurred'));
			echo template_simple ('error.spt', array ('msg' => $out));
		}
	}
}

?>