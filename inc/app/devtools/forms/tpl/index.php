<?php

if (! @is_writeable ('inc/html')) {
	page_title (intl_get ('An Error Occurred'));
	echo template_simple ('error.spt', array ('msg' => 'Your inc/html folder is not writeable by Sitellite.'));
	return;
}

loader_import ('devtools.Functions');

class DevtoolsTplForm extends MailForm {
	function DevtoolsTplForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/devtools/forms/tpl/settings.php');

		page_title (intl_get ('Create a New Template Set'));

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/devtools-app\'; return false"';
	}

	function onSubmit ($vals) {
		// your handler code goes here

		chdir ('inc/html');
		$out = shell_exec ('../app/devtools/bin/tpl.sh ' . escapeshellarg ($vals['setname']));
		chdir ('../..');

		if (trim ($out) == 'Your template set is ready, sir.') {
			chdir ('inc/html');
			shell_exec ('umask 0000; chmod -R 0777 ' . escapeshellarg ($vals['setname']));
			chdir ('../..');

			page_title (intl_get ('Template Set Created'));
			echo template_simple ('tpl_created.spt', $vals);
		} else {
			page_title (intl_get ('An Error Occurred'));
			echo template_simple ('error.spt', array ('msg' => $out));
		}
	}
}

?>