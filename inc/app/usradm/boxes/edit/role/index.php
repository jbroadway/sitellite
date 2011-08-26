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

page_add_script ('

/**
 * Implementation by Dustin Diaz.
 */
function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = \'*\';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

function resources_select_all (field) {
	checkboxes = getElementsByClass ("resources", document, "input");
	if (field.value == "r") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "r") {
				checkboxes[i].checked = field.checked;
			}
		}
	} else if (field.value == "w") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "w") {
				checkboxes[i].checked = field.checked;
			}
		}
	}
}

function access_select_all (field) {
	checkboxes = getElementsByClass ("access", document, "input");
	if (field.value == "r") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "r") {
				checkboxes[i].checked = field.checked;
			}
		}
	} else if (field.value == "w") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "w") {
				checkboxes[i].checked = field.checked;
			}
		}
	}
}

function status_select_all (field) {
	checkboxes = getElementsByClass ("status", document, "input");
	if (field.value == "r") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "r") {
				checkboxes[i].checked = field.checked;
			}
		}
	} else if (field.value == "w") {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].value == "w") {
				checkboxes[i].checked = field.checked;
			}
		}
	}
}

');

$snm =& session_get_manager ();
$form =& $snm->{$types[$cgi->_list]}->getEditForm ($cgi->_key);

if ($form->invalid ($cgi)) {
	$form->extra = 'class="usradm-role"';
	$form->setValues ($cgi);
	echo '<h1>' . intl_get ('Editing') . ': ' . ucwords ($names[$cgi->_list]) . '</h1>' . NEWLINEx2;
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();

	foreach ($vals['resources'] as $k => $v) {
		$vals['resources'][$k] = str_replace (',', '', $v);
		if (empty ($v)) {
			unset ($vals['resources'][$k]);
		}
	}

	foreach ($vals['accesslevels'] as $k => $v) {
		$vals['accesslevels'][$k] = str_replace (',', '', $v);
		if (empty ($v)) {
			unset ($vals['accesslevels'][$k]);
		}
	}

	foreach ($vals['statuses'] as $k => $v) {
		$vals['statuses'][$k] = str_replace (',', '', $v);
		if (empty ($v)) {
			unset ($vals['statuses'][$k]);
		}
	}

	$name = $vals['name'];
	$data = array (
		'role' => array (
			'name' => $vals['name'],
			'admin' => $vals['admin'],
			'disabled' => $vals['disabled'],
		),
		'allow:resources' => $vals['resources'],
		'allow:access' => $vals['accesslevels'],
		'allow:status' => $vals['statuses'],
	);

	$res = $snm->role->edit ($cgi->_key, $name, $data);
	if (! $res) {
		die ($snm->{$types[$cgi->_list]}->error);
	}

	db_execute (
		'update sitellite_user set role = ? where role = ? and expires is null',
		$vals['name'], // new
		$cgi->_key // old
	);

	global $session;

	db_execute (
		'update sitellite_user set role = ?, expires = ? where role = ? and expires is not null',
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