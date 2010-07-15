<?php

$on = appconf ('home');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'box:sitemember/home') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

$info = session_get_user ();

$name = '';
if (! empty ($info->firstname)) {
	$name .= $info->firstname;
	if (! empty ($info->lastname)) {
		$name .= ' ' . $info->lastname;
	}
} else {
	$name = $info->username;
}

page_title (intl_get ('Member Home') . ': ' . $name);

$data = array ();

$services = appconf ('member_services');

foreach ($services['home'] as $name => $service) {
	list ($type, $call) = explode (':', $service);
	$func = 'loader_' . $type;
	$data[$name] = $func (trim ($call), $parameters);
}

echo template_simple ('home.spt', $data);

?>