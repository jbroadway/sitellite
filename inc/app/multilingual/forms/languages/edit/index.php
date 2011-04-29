<?php

class MultilingualLanguagesEditForm extends MailForm {
	function MultilingualLanguagesEditForm () {
		parent::MailForm (__FILE__);

		global $cgi;

		if ($cgi->appname == 'GLOBAL') {
			$this->_file = 'inc/lang/languages.php';
		} else {
			$this->_file = 'inc/app/' . $cgi->appname . '/lang/languages.php';
		}

		$list = array ('no' => 'None');
		$info = ini_parse ($this->_file);
		foreach ($info as $k => $v) {
			// Cannot fallback to itself...
			if ($k != $cgi->lang) {
				$list[$k] = $v['name'];
			}
		}
		$this->widgets['fallback']->setValues ($list);

		if ($info[$cgi->lang]['default'] == 1) {
			$this->widgets['default']->setValues (array ('yes' => 'Yes'));
		}
		else {
			$this->widgets['default']->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		}	

		foreach ($info[$cgi->lang] as $k => $v) {
			if ($k == 'default') {
				if ($v == 1) {
					$this->widgets[$k]->setValue ('yes');
				} else {
					$this->widgets[$k]->setValue ('no');
				}
			} elseif ($k == 'fallback') {
				if (empty ($v)) {
					$this->widgets[$k]->setValue ('no');
				} else {
					$this->widgets[$k]->setValue ($v);
				}
			} else {
				$this->widgets[$k]->setValue ($v);
			}
		}

		page_title (intl_get ('Editing Language') . ': ' . $info[$cgi->lang]['name']);

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$file = $this->_file;
		$info = ini_parse ($file);

		if ($vals['default'] == 'yes') {
			foreach ($info as $l=>$v) {
				$info[$l]['default'] = 'no';
			}
		}

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

		if ($code != $vals['lang']) {
			unset ($info[$vals['lang']]);
		}

		$fp = fopen ($file, 'w');
		if (! $fp) {
			echo 'Error: Failed to open languages.php file!';
			return;
		}

		fwrite ($fp, ini_write ($info));
		fclose ($fp);

		/*header (
			sprintf (
				'Location: %s/index/appdoc-translation-strings-action?appname=%s&lang=%s&charset=%s',
				site_prefix (),
				$vals['appname'],
				$code,
				$vals['charset']
			)
		);*/
		header ('Location: ' . site_prefix () . '/index/multilingual-languages-action');
		exit;
	}
}

?>
