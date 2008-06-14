<?php

class SettingsForm extends MailForm {
	function SettingsForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/devfiles/forms/settings/settings.php');

		$this->widgets['files']->setValues (
			array (
				'on' => intl_get ('On'),
				'off' => intl_get ('Off'),
				'date' => intl_get ('Until date'),
			)
		);
		$this->widgets['submit_button']->setValues (intl_get ('Save Changes'));

		$this->widgets['files_date']->setValue (date ('Y-m-d'));

		if (DEVFILES === true) {
			$this->widgets['files']->setValue ('on');
		} elseif (DEVFILES === false) {
			$this->widgets['files']->setValue ('off');
		} else {
			$this->widgets['files']->setValue ('date');
			$this->widgets['files_date']->setValue (DEVFILES);
		}

		$this->widgets['contact']->setValue (appconf ('contact'));
		$this->widgets['ignore_list']->setValue (join (', ', appconf ('ignore')));
		$this->widgets['allowed_types']->setValue (join (', ', appconf ('allowed')));
		$this->widgets['not_allowed']->setValue (join (', ', appconf ('not_allowed')));
	}

	function onSubmit ($vals) {
		// update devnotes_config table and respond
		if ($vals['files'] == 'date') {
			$vals['files'] = $vals['notes_date'];
		}
		db_execute (
			'update devfiles_config set files = ?, contact = ?, ignore_list = ?, allowed_types = ?, not_allowed = ?',
			$vals['files'],
			$vals['contact'],
			$vals['ignore_list'],
			$vals['allowed_types'],
			$vals['not_allowed']
		);
		header ('Location: ' . site_prefix () . '/index/devfiles-admin-action');
		exit;
	}
}

$form = new SettingsForm ();
echo $form->run ();

?>