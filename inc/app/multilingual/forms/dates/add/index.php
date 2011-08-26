<?php

function dates_rule_unique_name ($vals) {
	$file = 'inc/lang/'.$vals['default'].'.dates.php';
	$info = ini_parse ($file);

	$a = array_keys ($info['formats']);

	return ! in_array ($vals['name'], $a);
}

class MultilingualDatesAddForm extends MailForm {
	function MultilingualDatesAddForm () {
		parent::MailForm (__FILE__);

		global $cgi;

		$this->_file = 'inc/lang/'.$cgi->lang.'.dates.php';

		$this->widgets['lang']->setValue ($cgi->lang);
		$this->widgets['charset']->setValue ($cgi->charset);
		$this->widgets['default']->setValue ($cgi->default);

		$info = ini_parse ($this->_file);

		page_title (intl_get ('Adding Date Strings') . ': ' . $cgi->lang);

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$file = $this->_file;
		$info = ini_parse ($file);

		$info['formats'][$vals['name']] = $vals['format_string'];

		$fp = fopen ($file, 'w');
		if (! $fp) {
			echo 'Error: Failed to open ';
			echo basename($file);
			echo ' file!';
			return;
		}

		fwrite ($fp, ini_write ($info));
		fclose ($fp);

		// Write to default file
		if ($vals['lang'] != $vals['default']) {
			$file = 'inc/lang/'.$vals['default'].'.dates.php';
			$info = ini_parse ($file);

			$info['formats'][$vals['name']] = $vals['format_string'];

			$fp = fopen ($file, 'w');
			if (! $fp) {
				echo 'Error: Failed to open ';
				echo basename($file);
				echo ' file!';
				return;
			}

			fwrite ($fp, ini_write ($info));
			fclose ($fp);
		}

		header ('Location: ' . site_prefix () . '/index/multilingual-dates-edit-action?lang=' . $vals['lang'] .
			'&charset=' . $vals['charset'] . '&default=' . $vals['default']);
		exit;
	}
}

?>
