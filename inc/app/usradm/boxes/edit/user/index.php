<?php

global $cgi;

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

function teams_select_all (field) {
	checkboxes = getElementsByClass ("teams", document, "input");
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
$form =& $snm->user->getEditForm ($cgi->_key);

if ($form->invalid ($cgi)) {
	$form->extra = 'id="usradm-user" class="usradm-user" autocomplete="off"';
	$form->widgets['passwd']->extra = 'autocomplete="off"';
	$form->setValues ($cgi);
	echo '<h1>' . intl_get ('Editing') . ': ' . ucwords ('User') . '</h1>' . NEWLINEx2;
	global $session;
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();

	foreach ($vals['teams'] as $k => $v) {
		$vals['teams'][$k] = str_replace (',', '', $v);
		if (empty ($vals['teams'][$k])) {
			unset ($vals['teams'][$k]);
		}
	}

	unset ($vals['_list']);
	unset ($vals['tab1']);
	unset ($vals['tab2']);
	unset ($vals['tab3']);
	unset ($vals['tab-end']);
	unset ($vals['password_verify']);
	unset ($vals['submit_button']);
	unset ($vals['registered']);
	$vals['modified'] = date ('Y-m-d H:i:s');

	if (! empty ($vals['passwd'])) {
		$vals['password'] = better_crypt ($vals['passwd']);
		unset ($vals['passwd']);
	} else {
		unset ($vals['passwd']);
	}

	$user = $vals['_key'];
	unset ($vals['_key']);

	$vals['lang'] = 'en'; // changeable via preferences later by user

	if ($vals['website'] == 'http://') {
		unset ($vals['website']);
	}

	if ($user == session_username ()) {
		global $session;
		$vals['expires'] = date ('YmdHis', time () + $session->timeout);
	}

	$snm->user->edit ($user, $vals);

	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=users');
	exit;

//	echo '<h1>' . intl_get ('Added') . ' ' . $names[$cgi->_list] . '</h1>' . NEWLINEx2;
//	echo template_simple ('<p><a href="{site/prefix}/index/usradm-browse-action?list={cgi/_list}">Back</a></p>');
}

//exit;

?>