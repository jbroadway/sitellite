<?php

loader_import ('saf.MailForm');

class PreferencesForm extends MailForm {
	function PreferencesForm () {
		parent::MailForm ();

		$this->extra = 'class="cms-preferences" autocomplete="off"';

		$this->help = session_pref ('form_help');

		if ($this->help == 'on') {
			// include formhelp
			page_add_script (site_prefix () . '/js/formhelp-compressed.js');
			page_add_script ('
				formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
				formhelp_append = \'</td></tr></table>\';
			');
		}

		if (db_shift ('select count(*) from sitellite_user where username = ?', session_username ())) {
			// we allow only internal users to change their passwords,
			// other sources are managed externally
			$this->internal_user = true;

			$w =& $this->addWidget ('section', 'pwdsection');
			$w->title = intl_get ('Change Password');

			$w =& $this->addWidget ('password', 'orig');
			$w->alt = intl_get ('Current Password');
			$w->addRule ('func "cms_user_preferences_pass_empty_rule"', intl_get ('You must enter your current password to change it.'));
			$w->addRule ('func "cms_user_preferences_pass_wrong_rule"', intl_get ('Your current password is incorrect.'));
			$w->extra = 'autocomplete="off"';
			$w->ignoreEmpty = false;

			$w =& $this->addWidget ('password', 'passwd');
			$w->alt = intl_get ('New Password');

			$w =& $this->addWidget ('password', 'password_verify');
			$w->alt = intl_get ('Verify Password');
			$w->addRule ('equals "passwd"', intl_get ('Passwords do not match.'));
			$w->ignoreEmpty = false;

			$w =& $this->addWidget ('section', 'prefsection');
			$w->title = intl_get ('Preferences');
		} else {
			$this->internal_user = false;
		}

		$this->parseSettings ('inc/conf/auth/preferences/index.php');

		$prefs = ini_parse ('inc/conf/auth/preferences/index.php');

		foreach ($prefs as $key => $values) {
			foreach ($values as $k => $v) {
				if (strpos ($k, 'value ') === 0) {
					if ($v === '1') {
						$v = 'on';
					} elseif ($v === '') {
						$v = 'off';
					}
					$this->widgets[$key]->setValues ($v, $v);
				} elseif ($k == 'values') {
					$values = $v ();
					$this->widgets[$key]->setValues ($values);
				} elseif ($this->help == 'on' && $k == 'instructions') {
					$this->widgets[$key]->extra = 'onfocus="formhelp_show (this, \'' . addslashes ($v) . '\')" onblur="formhelp_hide ()"';
				}
			}
			if ($key == 'browse_level') {
				$p = session_pref ($key);
				if ($p == 'normal') {
					$this->widgets[$key]->setValue ('easy');
				} else {
					$this->widgets[$key]->setValue ($p);
				}
			} else {
				$this->widgets[$key]->setValue (session_pref ($key));
			}
		}

		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));

		if (session_pref ('start_page') == 'web view') {
			$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index\'; return false"';
		} else {
			$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/cms-cpanel-action\'; return false"';
		}
	}
	function onSubmit ($vals) {
		unset ($vals['submit_button']);

		if ($this->internal_user) {
			unset ($vals['pwdsection']);
			unset ($vals['prefsection']);

			if (! empty ($vals['passwd'])) {
				// update password
				global $session;
				$session->update (array ('password' => better_crypt ($vals['passwd'])));
		
				// keep them logged in
				$session->username = session_username ();
				$session->password = $vals['passwd'];
				$session->start ();
			}

			unset ($vals['orig']);
			unset ($vals['passwd']);
			unset ($vals['password_verify']);
		}

		foreach ($vals as $key => $value) {
			if ($key == 'browse_level' && $value == 'easy') {
				session_pref_set ('browse_level', 'normal');
			} else {
				session_pref_set ($key, $value);
			}
		}

		//page_title (intl_get ('Preferences Saved'));
		//echo '<p>' . intl_get ('Your preferences have been saved.') . '</p>';
		//echo '<p><a href="' . site_prefix () . '/index/cms-app">' . intl_get ('Continue') . '</a></p>';

		session_set ('sitellite_alert', intl_get ('Your preferences have been saved.'));

		if (session_pref ('start_page') == 'web view') {
			header ('Location: ' . site_prefix () . '/index');
		} else {
			header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
		}

		exit;
	}
}

function cms_user_preferences_pass_empty_rule ($vals) {
	if (! empty ($vals['passwd']) && empty ($vals['orig'])) {
		return false;
	}
	return true;
}

function cms_user_preferences_pass_wrong_rule ($vals) {
	if (! empty ($vals['orig'])) {
		$current = db_shift ('select password from sitellite_user where username = ?', session_username ());
		if (! better_crypt_compare ($vals['orig'], $current)) {
			return false;
		}
	}
	return true;
}

page_title (intl_get ('Preferences'));
$form = new PreferencesForm;
echo $form->run ();

?>