<?php

if (! session_admin ()) {
	page_title (intl_get ('DevFiles - Login'));

	if (isset ($parameters['username'])) {
		echo '<p>' . intl_get ('Invalid password.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please enter your username and password to enter.') . '</p>';
	}

	echo loader_box ('sitellite/user/login', array ('goto' => 'devfiles-admin-action'));
	return;
}

loader_import ('devfiles.Files');

$fl = new Files;

// list all apps

page_title (intl_get ('DevFiles - Admin Summary'));

$apps = $fl->getApps ();

echo template_simple ('applist.spt', array ('apps' => $apps));

?>