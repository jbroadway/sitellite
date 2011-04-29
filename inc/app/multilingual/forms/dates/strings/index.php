<?php

function dates_rule_days ($vals) {
	return (count (explode (',', $vals['days'] )) == 7);
}

function dates_rule_shdays ($vals) {
	$a = explode (',', $vals['shortdays']);
	if (count ($a) != 7) {
		return false;
	}

	foreach ($a as $v) {
		if ( mb_strlen ( trim ($v), $vals['charset'] ) > 4) {
		    echo trim ($v);
		    echo $vals['charset'];
			return false;
		}
	}
	return true;
}

function dates_rule_months ($vals) {
	return (count (explode (',', $vals['months'] )) == 12);
}

function dates_rule_shmonths ($vals) {
	global $cgi;

	$a = explode (',', $vals['shortmonths']);
	if (count ($a) != 12) {
		return false;
	}
	foreach ($a as $v) {
		if ( mb_strlen ( trim ($v), $vals['charset'] ) != 3) {
			return false;
		}
	}
	return true;
}

function dates_rule_antepost ($vals) {
	return (count (explode (',', $vals['antepost'] )) == 2);
}

function dates_rule_suffixes ($vals) {
	return (count (explode (',', $vals['suffixes'] )) == 4);
}

class MultilingualDatesStringsForm extends MailForm {
	function MultilingualDatesStringsForm () {
		parent::MailForm (__FILE__);

		global $cgi;

		$this->_file = 'inc/lang/'.$cgi->lang.'.dates.php';

		$this->widgets['lang']->setValue ($cgi->lang);
		$this->widgets['charset']->setValue ($cgi->charset);

		if (file_exists ($this->_file)) {
			$info = ini_parse ($this->_file);

			foreach ($info['translations'] as $k => $v) {
				$this->widgets[$k]->setValue ($v);
			}
		}

		page_title (intl_get ('Editing Date Strings') . ': ' . $cgi->lang);

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$file = $this->_file;
		$info = ini_parse ($file);

		$a = array('days', 'shortdays', 'months', 'shortmonths', 'antepost', 'suffixes');
		foreach ($a as $n) {
			$info['translations'][$n] = $vals[$n];
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
			'&charset=' . $vals['charset']);
		exit;
	}
}

?>
