<?php

class SiteforumRegisterForm extends MailForm {
	function SiteforumRegisterForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteforum/forms/register/settings.php');

		page_title (intl_get ('Member Registration'));
	}

	function onSubmit ($vals) {
		$vals['public'] = ($vals['public']) ? 'yes' : 'no';

		if ($vals['website'] == 'http://') {
			$vals['website'] = '';
		}

		$session_id = session_make_pending_key ();
		$vals['verify'] = str_replace ('PENDING:', '', $session_id);

		// 1. insert into sitellite_user
		$res = db_execute ('
			insert into sitellite_user
				(username, password, firstname, lastname, company, website, country, province, email, session_id, role, team)
			values
				(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
			$vals['user_id'],
			better_crypt ($vals['password']),
			$vals['firstname'],
			$vals['lastname'],
			$vals['company'],
			$vals['website'],
			$vals['country'],
			$vals['province'],
			$vals['email'],
			$session_id,
			'member',
			'core'
		);
		if (! $res) {
			page_title ('Unknown Error');
			echo '<p>An error occurred while creating your account.  Please try again later.</p>';
			echo '<p>Error Message: ' . db_error () . '</p>';
			return;
		}

		// 2. insert into org_profile
		/*db_execute (
			'insert into org_profile
				(user_id, public, about, sig)
			values
				(?, ?, ?, ?)',
			$vals['user_id'],
			$vals['public'],
			$vals['about'],
			$vals['sig']
		);*/

		// 3. email confirmation
		@mail ($vals['email'], 'Membership Confirmation', template_simple ('member_confirmation.spt', $vals), 'From: ' . appconf ('email'));

		// 4. log them in
		//global $cgi, $session;
		//$cgi->username = $cgi->user_id;
		//$session->username = $cgi->user_id;
		//$session->password = $cgi->password;
		//$session->start ();

		// 5. respond
		page_title (intl_get ('Welcome') . ' ' . $vals['firstname'] . ' ' . $vals['lastname']);
		echo '<p>Your account has been created.  An email has also been sent to your address containing information necessary to activate your account.</p>';
	}
}

?>
