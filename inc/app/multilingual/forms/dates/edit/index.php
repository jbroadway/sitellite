<?php

function dates_rule_format_string ($vars) {
	if ($vars['lang'] == $vars['default']) {
		$s = trim ($vars['format_string']);
		if (empty ($s)) {
			return false;
		}
	}
	return true;
}

class MultilingualDatesEditForm extends MailForm {
	function MultilingualDatesEditForm () {
		parent::MailForm (__FILE__);

		global $cgi;

		$this->_file = 'inc/lang/'.$cgi->lang.'.dates.php';

		$this->widgets['lang']->setValue ($cgi->lang);
		$this->widgets['charset']->setValue ($cgi->charset);
		$this->widgets['default']->setValue ($cgi->default);

		$info = ini_parse ($this->_file);

		$this->widgets['name']->setValue ($cgi->format);
		$this->widgets['format_string']->setValue ($info['formats'][$cgi->format]);

		page_title (intl_get ('Editing Date Strings') . ': ' . $cgi->lang);

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$file = $this->_file;
		$info = ini_parse ($file);

		if (empty ($vals['format_string'])) {
			unset ($info['formats'][$vals['name']]);
		}
		else {
			$info['formats'][$vals['name']] = $vals['format_string'];
		}

		$fp = fopen ($file, 'w');
		if (! $fp) {
			echo 'Error: Failed to open ';
			echo basename($file);
			echo ' file!';
			return;
		}

		fwrite ($fp, ini_write ($info));
		fclose ($fp);

		header ('Location: ' . site_prefix () . '/index/multilingual-dates-edit-action?lang=' . $vals['lang'] .
			'&charset=' . $vals['charset'] . '&default=' . $vals['default']);
		exit;
	}
}

?>
