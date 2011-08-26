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

	$name = $vals['pref_name'];
	$data = array (
		'alt' => $vals['alt'],
		'instructions' => $vals['instructions'],
		'type' => 'select',
	);
	if (! empty ($vals['values'])) {
		$data['values'] = $vals['values'];
	} else {
		foreach (preg_split ('/[\r\n]+/s', $vals['value_list'], -1, PREG_SPLIT_NO_EMPTY) as $key => $value) {
			$data['value ' . ($key + 1)] = $value;
		}
	}
	$data['default_value'] = $vals['default_value'];

	$res = $snm->{$types[$cgi->_list]}->edit ($cgi->_key, $name, $data);
	if (! $res) {
		die ($snm->{$types[$cgi->_list]}->error);
	}

	//info ($name);
	//info ($data);

	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=' . $cgi->_list);
	exit;

//	echo '<h1>' . intl_get ('Added') . ' ' . $names[$cgi->_list] . '</h1>' . NEWLINEx2;
//	echo template_simple ('<p><a href="{site/prefix}/index/usradm-browse-action?list={cgi/_list}">Back</a></p>');
}

//exit;

?>