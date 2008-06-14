<?php

class SettingsForm extends MailForm {
	function SettingsForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/devnotes/forms/settings/settings.php');

		$this->widgets['notes']->setValues (
			array (
				'on' => intl_get ('On'),
				'off' => intl_get ('Off'),
				'date' => intl_get ('Until date'),
			)
		);
		$this->widgets['submit_button']->setValues (intl_get ('Save Changes'));

		$this->widgets['notes_date']->setValue (date ('Y-m-d'));

		if (DEVNOTES === true) {
			$this->widgets['notes']->setValue ('on');
		} elseif (DEVNOTES === false) {
			$this->widgets['notes']->setValue ('off');
		} else {
			$this->widgets['notes']->setValue ('date');
			$this->widgets['notes_date']->setValue (DEVNOTES);
		}

		$this->widgets['contact']->setValue (appconf ('contact'));
		$this->widgets['ignore_list']->setValue (join (', ', appconf ('ignore')));
	}

	function onSubmit ($vals) {
		// update devnotes_config table and respond
		if ($vals['notes'] == 'date') {
			$vals['notes'] = $vals['notes_date'];
		}
		db_execute (
			'update devnotes_config set notes = ?, contact = ?, ignore_list = ?',
			$vals['notes'],
			$vals['contact'],
			$vals['ignore_list']
		);
		header ('Location: ' . site_prefix () . '/index/devnotes-admin-action');
		exit;
	}
}

$form = new SettingsForm ();
echo $form->run ();

?>