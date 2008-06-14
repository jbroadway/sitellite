<?php

page_title (intl_get ('Adding') . ': ' . intl_get ('Role'));



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