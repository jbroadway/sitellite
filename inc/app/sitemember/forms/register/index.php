<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #198 Allow for HTML mailing templates.

$on = appconf ('register');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'form:sitemember/register') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

class SitememberRegisterForm extends MailForm {
	function SitememberRegisterForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemember/forms/register/settings.php');

		page_title (intl_get ('Member Registration'));
	}

	function onSubmit ($vals) {
		$vals['public'] = ($vals['public'] == '0') ? 'yes' : 'no';

		if ($vals['website'] == 'http://') {
			$vals['website'] = '';
		}

		$session_id = session_make_pending_key ();
		$vals['verify'] = str_replace ('PENDING:', '', $session_id);

		// 1. insert into sitellite_user
		$res = session_user_add (
			array (
				'username' => $vals['user_id'],
				'password' => better_crypt ($vals['password']),
				'firstname' => $vals['firstname'],
				'lastname' => $vals['lastname'],
				'company' => $vals['company'],
				'website' => $vals['website'],
				'country' => $vals['country'],
				'province' => $vals['province'],
				'email' => $vals['email'],
				'session_id' => $session_id,
				'role' => 'member',
				'team' => 'none',
				'public' => $vals['public'],
				'profile' => $vals['about'],
				'sig' => $vals['sig'],
				'registered' => date ('Y-m-d H:i:s'),
				'modified' => date ('Y-m-d H:i:s'),
			)
		);
		if (! $res) {
			page_title (intl_get('Unknown Error'));
			echo '<p>An error occurred while creating your account.  Please try again later.</p>';
			return;
		}
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
		// 2. email confirmation
//		@mail ($vals['email'], 'Membership Confirmation', template_simple ('register_confirmation.spt', $vals), 'From: ' . appconf ('email'));
//-----------------------------------------------
        // 2. email confirmation
		site_mail (
            $vals['email'],
            intl_get ('Membership Confirmation'),
            template_simple ('register_confirmation.spt', $vals),
            'From: ' . appconf ('email'),
            array ("Is_HTML" => true)
        );
//END: SEMIAS.
		// 3. respond
		page_title (intl_get ('Welcome') . ' ' . $vals['firstname'] . ' ' . $vals['lastname']);
		echo '<p>' . intl_get ('Your account has been created. An email has also been sent to your address containing information necessary to activate your account.') . '</p>';
	}
}

?>
