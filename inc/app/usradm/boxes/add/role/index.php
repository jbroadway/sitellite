<?php

page_title (intl_get ('Adding') . ': ' . intl_get ('Role'));

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
$form =& $snm->role->getAddForm ();


global $cgi;

if ($form->invalid ($cgi)) {
	$form->extra = 'class="usradm-role"';
	$form->setValues ($cgi);
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();

//	info ($vals);

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

	$snm->role->add ($name, $data);

	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=roles');
	exit;
}

return;

?>