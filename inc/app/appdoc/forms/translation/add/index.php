<?php

function lang_not_exists ($vals) {
	$code = $vals['code'];
	if (! empty ($vals['locale'])) {
		$code .= '-' . $vals['locale'];
	}

	if ($vals['appname'] == 'GLOBAL') {
		$info = ini_parse ('inc/lang/languages.php');
	} else {
		$info = ini_parse ('inc/app/' . $vals['appname'] . '/lang/languages.php');
	}
	if (isset ($info[$code])) {
		return false;
	}
	return true;
}

class AppdocTranslationAddForm extends MailForm {
	function AppdocTranslationAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/appdoc/forms/translation/add/settings.php');

		page_title (intl_get ('Add Language'));

		global $cgi;

		if ($cgi->appname == 'GLOBAL') {
			$this->_file = 'inc/lang/languages.php';
		} else {
			$this->_file = 'inc/app/' . $cgi->appname . '/lang/languages.php';
		}

		$list = array ('no' => 'None');
		$info = ini_parse ($this->_file);
		foreach ($info as $k => $v) {
			$list[$k] = $v['name'];
		}
		$this->widgets['fallback']->setValues ($list);

		$this->widgets['default']->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		if (count ($list) == 1) {
			$this->widgets['default']->setDefault ('yes');
		} else {
			$this->widgets['default']->setDefault ('no');
		}

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$file = $this->_file;
		$info = ini_parse ($file);

		$code = $vals['code'];
		if (! empty ($vals['locale'])) {
			$code .= '-' . $vals['locale'];
		}

		$info[$code] = array (
			'name' => $vals['name'],
			'code' => $vals['code'],
			'locale' => $vals['locale'],
			'charset' => $vals['charset'],
			'fallback' => $vals['fallback'],
			'default' => $vals['default'],
		);

		$fp = fopen ($file, 'w');
		if (! $fp) {
			echo 'Error: Failed to open languages.php file!';
			return;
		}

		fwrite ($fp, ini_write ($info));
		fclose ($fp);

		header (
			sprintf (
				'Location: %s/index/appdoc-translation-strings-action?appname=%s&lang=%s&charset=%s',
				site_prefix (),
				$vals['appname'],
				$code,
				$vals['charset']
			)
		);
		exit;
	}
}

?>