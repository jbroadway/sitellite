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

page_title ('Signup');

if (empty($parameters['stage'])) {
	echo template_simple ('user/signup.spt', $parameters);
} elseif (!strcmp($parameters['stage'], "essential")) {
	// Process the essential signup thingies
	$errormsg = create_user ($parameters['email'],
				 $parameters['Xusername'], // Use this to avoid starting a session
				 $parameters['password'],
				 $parameters['password_confirm'],
				 $parameters['firstname'],
				 $parameters['lastname']);
	
	if (empty($errormsg)) {
		// No errors.  User created.  Prompt for optional things.
		echo template_simple ('user/change.spt', $parameters);
	} else {
		// Had a problem during user creation.  Explain why and allow another chance.
		$parameters['errormsg'] = "<b>Error: " . $errormsg . "</b>"; // Is this the right way to handle errors in this system?
		echo template_simple ('user/signup.spt', $parameters);
	}
} else {
	// Hrm.  Complete waste of a stage mechanism :)
}

// Does this function belong in a seperate class or at least in a file in lib/ ?
function create_user ($email, $username, $password, $password_confirm, $firstname, $lastname) {
	if (!preg_match("/.+@.+\..+/", $email))
		return "Invalid email address";
	
	if (empty($username))
		return "Empty username";
	
	if (empty($password) || empty($password_confirm))
		return "Empty password";
	
	if (strcmp($password, $password_confirm))
		return "Passwords don't match";
	
	if (empty($firstname))
		return "Empty first name";
	
	if (empty($lastname))
		return "Empty last name";
	
	if (db_fetch("select email from sitellite_user where email = ?", $email))
		return "Email address is already in system";

	if (db_fetch("select username from sitellite_user where username = ?", $username))
		return "Username is already in system";

	// All the data checks out, let's insert this mofo
	if (!db_execute("insert into sitellite_user (email, password, firstname, lastname, username, role, team, expires, lang) values (?, ?, ?, ?, ?, ?, ?, ?, ?)", $email, crypt($password), $firstname, $lastname, $username, '', '', 0, 'en'))
		return "User creation failed";
	
	return ''; // Everything is peachy!  *gulp*
}

?>