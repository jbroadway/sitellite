<?php

loader_import ('saf.MailForm');

class AddForm extends MailForm {
	function AddForm () {
		parent::MailForm ();

		global $cgi;

		$this->addWidget ('hidden', 'appname');
		$this->addWidget ('hidden', 'lang');

		$w =& $this->addWidget ('text', 'lang_code');
		$w->alt = intl_get ('Language Code (ie. en)');
		$w->addRule ('not empty', intl_get ('You must specify a language code.'));

		$w =& $this->addWidget ('text', 'lang_name');
		$w->alt = intl_get ('Language Name');
		$w->addRule ('not empty', intl_get ('You must specify a language name.'));

		loader_import ('help.Help');

		$w =& $this->addWidget ('select', 'copy_from');
		$w->alt = intl_get ('Copy From (Optional)');
		$w->setValues (
			array_merge (
				array ('' => '- ' . intl_get ('SELECT') . ' -'),
				help_get_langs ($cgi->appname)
			)
		);

		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('submit_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $cgi->appname . '&lang=' . $cgi->lang . '\'; return false"';
	}

	function onSubmit ($vals) {
		loader_import ('saf.File');
		loader_import ('saf.File.Directory');
		loader_import ('saf.Misc.Ini');

		$info = help_get_langs ($vals['appname']);

		$info[$vals['lang_code']] = $vals['lang_name'];

		if (! @mkdir (site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang_code'], 0777)) {
			echo '<p>Error: Unable to create language folder.  Please verify your folder permissions.</p>';
			return;
		}

		if (! file_overwrite (
			site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/languages.php',
			ini_write ($info)
		)) {
			echo '<p>Error: Unable to write to the file.  Please verify your folder permissions.</p>';
			return;
		}

		if (! empty ($vals['copy_from'])) {
			// copy help files from specified lang to new dir
			$pages = help_get_pages ($vals['appname'], $vals['lang']);
			foreach ($pages as $page) {
				$id = help_get_id ($page);
				$res = copy (
					site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang'] . '/' . $id . '.html',
					site_docroot () . '/inc/app/' . $vals['appname'] . '/docs/' . $vals['lang_code'] . '/' . $id . '.html'
				);
				if (! $res) {
					echo '<p>Error: Unable to duplicate help files.  Please verify your folder permissions.</p>';
					return;
				}
			}
		}

		// go to new language
		header ('Location: ' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $vals['appname'] . '&lang=' . $vals['lang_code']);
		exit;
	}
}

page_title (intl_get ('Adding Language'));
$form = new AddForm ();
echo $form->run ();

?>