<?php

$info = ini_parse (getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/config.ini.php', false);
if (isset ($info['app_name'])) {
	$name = $info['app_name'];
} else {
	$name = ucfirst ($file);
}

page_title (intl_get ('Application Settings') . ' - ' . $name);

if (! is_writeable (getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/settings.ini.php')) {
	echo '<p class="invalid">' . intl_getf (
		'Warning: The settings file is not writeable. Please verify that the file %s is writeable by the web server user.',
		'inc/app/' . $parameters['appname'] . '/conf/settings.ini.php'
	) . '</p>';
	return;
}

global $intl;
$old_intl_path = $intl->directory;
$intl->directory = 'inc/app/' . $parameters['appname'] . '/lang';
$intl->getIndex ();
$intl->directory = $old_intl_path;

$settings = ini_parse (getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/settings.ini.php', true);

global $cgi;

loader_import ('saf.MailForm');

$form = new MailForm;

$w =& $form->addWidget ('hidden', 'appname');
$w->setValue ($parameters['appname']);

foreach ($settings as $k => $v) {
	if (! isset ($v['type'])) {
		$v['type'] = 'text';
	}
	if (isset ($v['value'])) {
		$val = $v['value'];
		unset ($v['value']);
	} else {
		$val = false;
	}
	$w =& $form->createWidget ($k, $v);
	if ($val) {
		$w->setValue ($val);
	}
}

$sub =& $form->addWidget ('msubmit', 'submit_button');
$b1 =& $sub->getButton ();
$b1->setValues (intl_get ('Save'));
$b2 =& $sub->addbutton ('submit_button', intl_get ('Cancel'));
$b2->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-applications-action\'; return false"';


if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	echo $form->show ();
} else {
	$vals = $form->getValues ();

	foreach ($vals as $k => $v) {
		if ($k == 'appname' || $k == 'submit_button') {
			continue;
		}
		if (isset ($v)) {
			$settings[$k]['value'] = $v;
		}
	}

	loader_import ('saf.File');
	if (! file_overwrite (
		getcwd () . '/inc/app/' . $parameters['appname'] . '/conf/settings.ini.php',
		ini_write ($settings)
	)) {
		die ('Error writing to file: inc/app/' . $parameters['appname'] . '/conf/settings.ini.php');
	}

	echo '<p>' . intl_get ('Application settings saved.') . ' <a href="' . site_prefix () . '/index/usradm-applications-action">' . intl_get ('Continue') . '</a></p>';
}

?>
