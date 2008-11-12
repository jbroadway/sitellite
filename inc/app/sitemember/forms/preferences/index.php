<?php

$on = appconf ('preferences');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'form:sitemember/preferences') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

class SitememberPreferencesForm extends MailForm {
	function SitememberPreferencesForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemember/forms/preferences/settings.php');

		page_title (intl_get ('Preferences'));

		$user = session_get_user ();
		foreach (get_object_vars ($user) as $k => $v) {
			if (is_object ($this->widgets[$k])) {
				if ($k == 'public') {
					if ($v == 'yes') {
						$this->widgets[$k]->setValue ($this->widgets['public']->value[array_shift (array_keys ($this->widgets['public']->value))]);
					}
				} else {
					$this->widgets[$k]->setValue ($v);
				}
			}
		}
	}

	function onSubmit ($vals) {
		$vals['public'] = ($vals['public']) ? 'yes' : 'no';

		if ($vals['website'] == 'http://') {
			$vals['website'] = '';
		}

		// 1. update sitellite_user
		$res = session_user_edit (
			session_username (),
			array (
				'firstname' => $vals['firstname'],
				'lastname' => $vals['lastname'],
				'company' => $vals['company'],
				'website' => $vals['website'],
				'country' => $vals['country'],
				'province' => $vals['province'],
				'email' => $vals['email'],
				'expires' => date ('Y-m-d H:i:s', time () + 3600),
				'public' => $vals['public'],
				'profile' => $vals['profile'],
				'sig' => $vals['sig'],
				'modified' => date ('Y-m-d H:i:s'),
			)
		);
		if (! $res) {
			page_title ('Unknown Error');
			echo '<p>' . intl_get ('An error occurred while updating your account.  Please try again later.') . '</p>';
			return;
		}

		// 2. respond
		page_title (intl_get ('Preferences Saved'));
		echo template_simple ('<p>{intl Your account information been updated.} <a href="{site/prefix}/index/sitemember-app">{intl Click here to continue.}</a></p>');
	}
}

?>
