<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

global $session, $site;

switch ($box['context']) {
	case 'inline': // Not sure what "inline" is
		echo "Inline";
	case 'normal': // Code should work fine with "normal" context
		echo "Normal";
	case 'action': // Most often called with "action" tho
		/*echo $parameters['username'] . ", " . $parameters['password'] . "<br />";
		echo "Action, params=" . count($parameters) . "<br />";
		reset($parameters);
		foreach ($parameters as $key => $value)
			echo "$key = $value<br />";*/
		if (empty ($parameters['email'])) {
			// Just present the form
			echo template_simple ('user/forgot/email.spt', $parameters);
		} else {
			// Not very elegant way of getting a new password
			$password = strval(mt_rand(33, 126)) . strval(mt_rand(33, 126)) . strval(mt_rand(33, 126)) . strval(mt_rand(33, 126)) . strval(mt_rand(33, 126)) . strval(mt_rand(33, 126));
			$crypted = crypt($password);
			$email = $parameters['email'];
			$q = $GLOBALS['db']->query("SELECT email FROM sitellite_user WHERE email = '$email'");
			if ($q->execute() && $q->fetch() != false) {
				// The email address is in the database
				$q2 = $GLOBALS['db']->query("UPDATE sitellite_user SET password = '$crypted' WHERE email = '$email'"); // K, HUGE SECURITY HOLE WITH FANCY SETTINGS OF $email VARIABLE!!!
				if(!$q2->execute())
					// Failed execution :(
					$errormsg = $q2->error;
				$q2->free();
			} else {
				if (empty($q->error))
					$errormsg = "Email address not in databse";
				else
					$errormsg = $q->error;
			}
			$q->free();
			if(empty($errormsg)) {
				// Everything went smoothly
				mail ($parameters['email'], "New sitellite.org password", "Your new password is: " . $password);
				echo template_simple ('user/login/normal.spt', $parameters);
			} else {
				// Couldn't update to new password - try again
				$parameters['errormsg'] = "<b>Error: " . $errormsg . "</b><br />";
				echo template_simple ('user/forgot/email.spt', $parameters);
			}
		}
		break;
}

?>