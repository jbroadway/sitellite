<?php

loader_import ('siteevent.Event');
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

if (! isset ($parameters['simplecal'])) {
	global $cgi;

	if ($context != 'action' && isset ($cgi->simplecal)) {
		$parameters['simplecal'] = $cgi->simplecal;
	} else {
		$parameters['simplecal'] = date ('Y-m');
	}
}

// if date is past one year from present, tell robots to skip
$cy = date ('Y');
$cm = date ('m');
list ($y, $m) = split ('-', $parameters['simplecal']);
if ($y > ($cy + 1) || $y < ($cy - 1) || ($y == ($cy + 1) && $m >= $cm) || ($y == ($cy - 1) && $m <= $cm)) {
	page_add_meta ('robots', 'noindex,nofollow');
}

$cal = new SimpleCal ($parameters['simplecal']);

$e = new SiteEvent_Event;


if (! isset ($parameters['view'])) {
	$parameters['view'] = appconf ('default_view');
}

if (appconf ('css_location') && $box['context'] != 'action') {
	$css = join ('', file (preg_replace ('|^' . site_prefix () . '/|', '', appconf ('css_location'))));
	echo '<style type="text/css">' . $css . '</style>';
}

if ($parameters['view'] == 'day') {
	include_once ('inc/app/siteevent/boxes/index/_day.php');
	return;
} elseif ($parameters['view'] == 'week') {
	include_once ('inc/app/siteevent/boxes/index/_week.php');
	return;
} elseif ($parameters['view'] == 'list') {
	include_once ('inc/app/siteevent/boxes/index/_list.php');
	return;
}

include_once ('inc/app/siteevent/boxes/index/_month.php');

?>