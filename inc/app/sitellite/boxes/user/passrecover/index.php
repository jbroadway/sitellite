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

global $cgi, $session;

if (empty ($cgi->email)) {

	// ask for email address
	if ($box['context'] == 'action') {
		page_title (intl_get ('Recover your password'));
	}
	echo template_simple ('user/passrecover/email.spt', $cgi);

} elseif (! empty ($cgi->key)) {

	// verify key
	$cgi->user = $session->getUserByEmail ($cgi->email);
	if (! $cgi->user) {
		if ($box['context'] == 'action') {
			page_title (intl_get ('Recover your password'));
		}
		echo template_simple ('user/passrecover/emailnotfound.spt', $cgi);
		return;
	}

	if (! $session->isValidKey ($cgi->user, 'RECOVER:' . $cgi->key)) {
		header ('Location: ' . site_prefix () . '/index/sitellite-user-passrecover-action');
		exit;
	}

	if (! empty ($cgi->password) && $cgi->verify == $cgi->password) {

		// update password
		$session->update (
			array ('password' => better_crypt ($cgi->password)),
			$cgi->user
		);

		$session->username = $cgi->user;
		$session->password = $cgi->password;
		$session->start ();
		if ($box['context'] == 'action') {
			page_title (intl_get ('Your password has been changed'));
		}

	} else {

		if ($cgi->verify != $cgi->password) {
			$cgi->error = true;
		}

		// prompt for new password
		if ($box['context'] == 'action') {
			page_title (intl_get ('Choose a new password'));
		}
		echo template_simple ('user/passrecover/newpass.spt', $cgi);

	}

} elseif (! empty ($cgi->email)) {

	// verify key
	$cgi->user = $session->getUserByEmail ($cgi->email);
	if (! $cgi->user) {
		if ($box['context'] == 'action') {
			page_title (intl_get ('Recover your password'));
		}
		echo template_simple ('user/passrecover/emailnotfound.spt', $cgi);
		return;
	}

	// generate and send key
	$cgi->key = $session->makeRecoverKey ();
	$session->update (
		array ('session_id' => $cgi->key),
		$cgi->user
	);
	$cgi->key = substr ($cgi->key, 8);
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//	if (! @mail ($cgi->email, intl_get ('Password Recovery Info'), template_simple ('user/passrecover/message.spt', $cgi), 'From: ' . $parameters['from'])) {
//-----------------------------------------------
    if (! site_mail (
            $cgi->email,
            intl_get ('Password Recovery Info'),
            template_simple ('user/passrecover/message.spt', $cgi),
            'From: ' . $parameters['from'],
            array ("Is_HTML" => true)
            ))
    {
//END: SEMIAS.
		echo '<p class="error">Error: Unknown mail transfer failure!</p>';
		return;
	}

	if ($box['context'] == 'action') {
		page_title (intl_get ('Check your email'));
	}
	echo template_simple ('user/passrecover/sent.spt', $cgi);

}

?>