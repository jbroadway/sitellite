<?php

//include_once('inc/lib/BadDesign101.php');

switch ($box['context']) {
case 'action':
	page_title ('Change Password');
	if ($parameters['command'] == 'save') {
		if (! session_valid ()) {
			echo loader_box ('sitellite/user/password', null); // The error message will handle itself
			return;
		}

		if (empty ($parameters['password_new_1']) ||
		    empty ($parameters['password_new_2'])) {
			// They gotta fill out all 3 fields
			echo loader_box ('sitellite/user/password', array ('errormsg' => 'You have to fill in both password fields with your new password'));
			return;
		}

		if ($parameters['password_new_1'] != $parameters['password_new_2']) {
			echo loader_box ('sitellite/user/password', array ('errormsg' => 'Your passwords do not match'));
			return;
		}

		$crypted = better_crypt ($parameters['password_new_1']);

		$res = db_execute ("update sitellite_user set password = ?, expires = now() + 3600 where username = ?", $crypted, $session->username);

		if (! $res) {
			echo loader_box ('sitellite/user/password', array ('errormsg' => 'Database error: ' . db_error ()));
			return;
		}

		if (! isset ($parameters['goto'])) {
			$parameters['goto'] = '';
		} else {
			$parameters['goto'] = '/' . $parameters['goto'];
		}

		page_title (intl_get ('Password Changed'));
		echo template_simple ('user/password_saved.spt', $parameters);

		return;
	}
 case 'inline':
 case 'normal':
 	if (! session_valid ()) {
 		$parameters['errormsg'] .= 'You must be logged in to change your password.';
	}
	echo template_simple ('user/password.spt', $parameters);
	break;
}

?>