<?php

$types = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access',
);

$names = array (
	'users'			=> 'user',
	'roles'			=> 'role',
	'teams'			=> 'team',
	'resources'		=> 'resource',
	'statuses'		=> 'status',
	'accesslevels'	=> 'access level',
);

global $cgi;

if (! in_array ($cgi->_list, array_keys ($types))) {
	header ('Location: ' . site_prefix () . '/index/usradm-browse-action');
	exit;
}

$snm =& session_get_manager ();
$form =& $snm->{$types[$cgi->_list]}->getAddForm ();

if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	echo '<h1>' . intl_get ('Adding') . ': ' . $names[$cgi->_list] . '</h1>' . NEWLINEx2;
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();
	unset ($vals['_list']);
	unset ($vals['submit_button']);
	$res = $snm->{$types[$cgi->_list]}->add ($vals);
	if (! $res) {
		die ($snm->{$types[$cgi->_list]}->error);
	}
	echo '<h1>' . intl_get ('Added') . ' ' . $names[$cgi->_list] . '</h1>' . NEWLINEx2;
	echo template_simple ('<p><a href="{site/prefix}/index/usradm-browse-action?list={cgi/_list}">Back</a></p>');
}

exit;

?>