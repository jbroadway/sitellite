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

page_title ('Change');

$errormsg = "";

// Gotta get ourselves a username somehow
if ($session->valid) {
	$Xusername = $session->username;
} elseif (!empty($parameters['Xusername'])) {
	$Xusername = $parameters['Xusername'];
} else {
	$errormsg = "<b>Error: No username found</b>";
	echo template_simple ('user/change.spt', $parameters);
	return;
}

if (empty($parameters['stage'])) {
	$res = db_fetch("select * from sitellite_user where username = ?", $Xusername);

	if (!res)
		$errormsg = "<b>Error: " . db_error() . "</b>";

	$parameters = array_merge($parameters, $res);
	
	$parameters['errormsg'] = $errormsg; // Is this the right way to handle errors in this system?
	echo template_simple ('user/change.spt', $parameters);
} elseif (!strcmp($parameters['stage'], "optional")) {
	// Process the essential signup thingies
	$errormsg = update_user ($parameters['username'] = $Xusername,
				 $parameters['email'],
				 $parameters['company'],
				 $parameters['position'],
				 $parameters['website'],
				 $parameters['jabber_id'],
				 $parameters['sms_address'],
				 $parameters['phone'], // Redundancy
				 $parameters['cell'],
				 $parameters['home'],  // Redundancy
				 $parameters['fax'],
				 $parameters['address1'],
				 $parameters['address2'],
				 $parameters['city'],
				 $parameters['province'],
				 $parameters['postal_code'],
				 $parameters['country']
				 ); // Good lord, perhaps I better start using structs/arrays!
	
	if (empty($errormsg)) {
		// No errors.  User record updated.  Load SOMETHING?!
		echo template_simple ('user/login/home.spt', $parameters);
	} else {
		// Had a problem during user creation.  Explain why and allow another chance.
		$parameters['errormsg'] = "<b>Error: " . $errormsg . "</b>"; // Is this the right way to handle errors in this system?
		echo template_simple ('user/change.spt', $parameters);
	}
} else {
	// Hrm.  Complete waste of a stage mechanism :)
}

// Does this function belong in a seperate class or at least in a file in lib/ ?
function update_user ($username, $email, $company, $position, $website, $jabber_id, $sms_address, $phone, $cell, $home, $fax, $address1, $address2, $city, $province, $postal_code, $country) {
	if (empty($email))
		return "No email address";

	if (!preg_match("/.+@.+\..+/", $email))
		return "Invalid email address";

	// This probably has structure for being done elsewhere - nasty hard coding
	$res = db_execute("update sitellite_user set expires = now() + 1800, email = ?, company = ?, position = ?, website = ?, jabber_id = ?, sms_address = ?, phone = ?, cell = ?, home = ?, fax = ?, address1 = ?, address2 = ?, city = ?, province = ?, postal_code = ?, country = ? WHERE username = ?",
			  $email,
			  $company,
			  $position,
			  $website,
			  $jabber_id,
			  $sms_address,
			  $phone,
			  $cell,
			  $home,
			  $fax,
			  $address1,
			  $address2,
			  $city,
			  $province,
			  $postal_code,
			  $country,
			  $username);
	
	if (!$res)
		$errormsg = db_error();
	
	return $errormsg; // Everything is peachy!  *gulp*
}

?>