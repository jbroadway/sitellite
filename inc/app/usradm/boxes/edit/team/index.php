<?php

$types = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
	'prefs'			=> 'pref',
);

$names = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access level',
	'prefs'			=> 'preference',
);

global $cgi;

if (! in_array ($cgi->_list, array_keys ($types))) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action');
	exit;
}

$snm =& session_get_manager ();
$form =& $snm->{$types[$cgi->_list]}->getEditForm ($cgi->_key);

if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	echo '<h1>' . intl_get ('Editing') . ': ' . ucwords ($names[$cgi->_list]) . '</h1>' . NEWLINEx2;
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();

	unset ($vals['_list']);
	unset ($vals['_key']);
	unset ($vals['submit_button']);
	unset ($vals['cancel_button']);

	$name = $vals['name'];
	if ($vals['disabled'] == 'yes') {
		$disabled = true;
	} else {
		$disabled = false;
	}
	$description = $vals['description'];

	$res = $snm->{$types[$cgi->_list]}->edit ($cgi->_key, $name, $disabled, $description);
	if (! $res) {
		die ($snm->{$types[$cgi->_list]}->error);
	}

	db_execute (
		'update sitellite_user set team = ? where team = ? and expires is null',
		$vals['name'], // new
		$cgi->_key // old
	);

	global $session;

	db_execute (
		'update sitellite_user set team = ?, expires = ? where team = ? and expires is not null',
		$vals['name'], // new
		date ('Y-m-d H:i:s', time () + $session->timeout),
		$cgi->_key // old
	);

	//info ($name);
	//info ($data);

	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list);
	exit;

//	echo '<h1>' . intl_get ('Added') . ' ' . $names[$cgi->_list] . '</h1>' . NEWLINEx2;
//	echo template_simple ('<p><a href="{site/prefix}/index/usradm-browse-action?list={cgi/_list}">Back</a></p>');
}

//exit;

?>