<?php

page_title (intl_get ('Adding') . ': ' . intl_get ('User'));

$snm =& session_get_manager ();
$form =& $snm->user->getAddForm ();


global $cgi;

if ($form->invalid ($cgi)) {
	$form->extra = 'id="usradm-user" class="usradm-user" autocomplete="off"';
	$form->widgets['_username']->extra = 'autocomplete="off"';
	$form->widgets['passwd']->extra = 'autocomplete="off"';
	$form->setValues ($cgi);
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

	$vals['password'] = better_crypt ($vals['passwd']);
	unset ($vals['passwd']);

	$vals['username'] = $vals['_username'];
	unset ($vals['_username']);

	$vals['lang'] = 'en'; // changeable via preferences later by user

	if ($vals['website'] == 'http://') {
		unset ($vals['website']);
	}

	$vals['registered'] = date ('Y-m-d H:i:s');
	$vals['modified'] = date ('Y-m-d H:i:s');

	if (! $snm->user->add ($vals)) {
		die ($snm->user->error);
	}

	header ('Location: ' . site_prefix () . '/index/usradm-browse-action?list=users');
	exit;
}

return;

?>