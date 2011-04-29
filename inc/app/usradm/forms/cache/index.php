<?php

page_title (intl_get ('Cache Settings'));

if (! is_writeable ('inc/conf/cache.php')) {
	echo '<p class="invalid">' . intl_get ('Warning: The cache file is not writeable.  Please verify that the file \'inc/conf/cache.php\' is writeable by the web server user.') . '</p>';
	return;
}

class UsradmCacheForm extends MailForm {
	var $ignore = array (
		'/index/appdoc-*',
		'/index/boxchooser-*',
		'/index/cms-*',
		'/index/filechooser-*',
		'/index/formchooser-*',
		'/index/imagechooser-*',
		'/index/myadm-*',
		'/index/scheduler-*',
		'/index/siteconnector-*',
		'/index/sitemailer-*',
		'/index/sitemailer2-*',
		'/index/sitemember-*',
		'/index/sitesearch-*',
		'/index/sitetemplate-*',
		'/index/sitetracker-*',
		'/index/usradm-*',
		'/index/xed-*',
	);
	function UsradmCacheForm () {
		parent::MailForm (__FILE__);

		page_add_style ('
			td.label {
				width: 200px;
			}
			td.field {
				padding-left: 7px;
				padding-right: 7px;
			}
		');

		$cache = ini_parse ('inc/conf/cache.php');
		foreach ($cache['Cache'] as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
		$cacheable = '';
		foreach ($cache['Cacheable'] as $k => $v) {
			if (in_array ($k, $this->ignore)) {
				continue;
			}
			if ($v) {
				$cacheable .= $k . " = yes\n";
			} else {
				$cacheable .= $k . " = no\n";
			}
		}
		$this->widgets['cacheable']->setValue ($cacheable);
	}

	function onSubmit ($vals) {
		// overwrite file
		$cache = array ('Cache' => array (), 'Cacheable' => array ());
		$cacheable = $vals['cacheable'];
		unset ($vals['cacheable']);
		unset ($vals['submit_button']);
		foreach ($vals as $k => $v) {
			$cache['Cache'][$k] = $v;
		}

		$cacheable = ini_parse ($cacheable, false);
		foreach ($this->ignore as $i) {
			$cacheable[$i] = false;
		}
		$cache['Cacheable'] = $cacheable;

		loader_import ('saf.File');
		if (! file_overwrite ('inc/conf/cache.php', ini_write ($cache))) {
			die ('Error writing to file: inc/conf/cache.php');
		}

		echo '<p>' . intl_get ('Cache settings saved.') . '  <a href="' . site_prefix () . '/index/cms-cpanel-action">' . intl_get ('Continue') . '</a></p>';
	}
}

?>