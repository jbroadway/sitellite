<?php

if (! session_admin ()) {
	page_title (intl_get ('DevNotes - Login'));

	if (isset ($parameters['username'])) {
		echo '<p>' . intl_get ('Invalid password.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please enter your username and password to enter.') . '</p>';
	}

	echo loader_box ('sitellite/user/login', array ('goto' => 'devnotes-admin-action'));
	return;
}

loader_import ('devnotes.DevNote');

$dn = new DevNote;

// list all apps

page_title ('DevNotes - Admin Summary');

$apps = $dn->getApps ();

echo template_simple ('applist.spt', array ('apps' => $apps));

?>