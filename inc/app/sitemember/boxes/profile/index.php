<?php

if (! isset ($parameters['user'])) {
	$parameters['user'] = session_username ();
}

$on = appconf ('profile');
if (! $on && $parameters['user'] != session_username ()) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'box:sitemember/profile') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), $parameters, $context);
	return;
}

$info = session_get_user ($parameters['user']);

if ($info->public != 'yes' && $parameters['user'] != session_username ()) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
}

$name = '';
if (! empty ($info->firstname)) {
	$name .= $info->firstname;
	if (! empty ($info->lastname)) {
		$name .= ' ' . $info->lastname;
	}
} else {
	$name = $info->username;
}

if ($box['context'] == 'action') {
	page_title (intl_get ('Member Profile') . ': ' . $name);
}

$data = array ();

$services = appconf ('member_services');

foreach ($services['profile'] as $name => $service) {
	list ($type, $call) = split (':', $service);
	$func = 'loader_' . $type;
	$data[$name] = $func (trim ($call), $parameters);
}

echo template_simple ('profile.spt', $data);

?>