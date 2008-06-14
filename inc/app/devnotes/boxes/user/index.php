<?php

global $page, $cgi;

if (isset ($cgi->appname)) {
	$appname = $cgi->appname;
	$inframe = true;
} else {
	$appname = $page->id;
	$inframe = false;
}

loader_import ('devnotes.DevNote');
$dn = new DevNote;

if (! empty ($parameters['style'])) {
	echo '<link rel="stylesheet" type="text/css" href="' . $parameters['style'] . '" />';
}

$title = 'DevNotes';
if (isset ($parameters['title'])) {
	$title = $parameters['title'];
}

echo '<a name="devnotes"></a><h1>' . intl_get ($title) . '</h1>';

if (session_admin ()) {
	echo template_simple ('<p><a href="{site/prefix}/index/devnotes-admin-action">{intl DevNotes Admin}</a></p>');
}

// display thread

$colours = appconf ('colours');
$defaultColour = 'transparent';

if ($parameters['highlight'] == 'no') {
	$colours = array ();
}

$colour = array_shift ($colours);

$thread = $dn->userMessages ($cgi->user);

if (! $thread) {
	echo template_simple ('error.spt', $dn);
	if ($parameters['exit'] == 'yes') {
		exit;
	}
	return;
}

echo template_simple ('user.spt', array ('user' => $cgi->user, 'colour' => $colour, 'inframe' => $inframe, 'thread' => $thread));

if ($parameters['exit'] == 'yes') {
	exit;
}

?>