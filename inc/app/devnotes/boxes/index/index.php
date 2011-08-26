<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #198 Allow for HTML mailing templates.
//

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

if (DEVNOTES === true || (is_string (DEVNOTES) && strtotime (DEVNOTES) > time ())) {
	$_devnotes = true;
} else {
	$_devnotes = false;
}

if (! empty ($parameters['style'])) {
	echo '<link rel="stylesheet" type="text/css" href="' . $parameters['style'] . '" />';
}

// post message

if ($_devnotes) {

if (! empty ($parameters['body'])) {
	if (empty ($parameters['name'])) {
		$parameters['name'] = $parameters['nameChooser'];
	}
	$res = $dn->add (
		array (
			'body' => $parameters['body'],
			'name' => $parameters['name'],
			'appname' => $appname,
		)
	);
	if (! $res) {
		echo '<h1>' . intl_get ('DevNotes') . '</h1>';
		echo template_simple ('error.spt', $dn);
		return;
	}

	$contact = appconf ('contact');
	if ($contact && ! in_array ($parameters['name'], appconf ('ignore'))) {
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//		@mail ($contact, intl_get ('DevNotes Notice'), template_simple ('email.spt', $parameters));
//-----------------------------------------------
        site_mail (
            $contact,
            intl_get ('DevNotes Notice'),
            template_simple ('email.spt', $parameters),
            array ("Is_HTML" => true)
        );
//END: SEMIAS.
	}

	// on success, forward back to referring page
	if (isset ($cgi->appname)) {
		header ('Location: ' . $_SERVER['HTTP_REFERER'] . '#devnotes');
		exit;
	} else {
		header ('Location: ' . $appname . '#devnotes');
		exit;
	}
}

} // end if $_devnotes

$title = 'DevNotes';
if (isset ($parameters['title'])) {
	$title = $parameters['title'];
}

echo '<a name="devnotes"></a><h1>' . intl_get ($title) . '</h1>';

if (session_admin ()) {
	if ($inframe) {
		$target = ' target="_parent"';
	} else {
		$target = '';
	}
	echo template_simple ('<p><a href="{site/prefix}/index/devnotes-admin-action"' . $target . '>{intl DevNotes Admin}</a></p>');
}

if ($_devnotes) {

	if (
			! isset ($parameters['posting']) ||
			(
				isset ($parameters['posting']) &&
				session_allowed ($parameters['posting'], 'rw', 'access')
			)
	) {

// display add form

$dn->listFields ('distinct name');
$dn->orderBy ('ts asc');
$users1 = $dn->find (array ());
if (! is_array ($users1)) {
	$users1 = array ();
}
$users = array ();
foreach ($users1 as $key => $value) {
	$users[] = $value->name;
}

echo template_simple ('form.spt', array ('users' => $users, 'appname' => $appname));

	} // end $parameters['posting'] check

} // end if $_devnotes

// display thread

$dn->listFields ('distinct name');
$dn->orderBy ('ts asc');
$users1 = $dn->find (
	array (
		'appname' => $appname,
	)
);
if (! is_array ($users1)) {
	$users1 = array ();
}
$users = array ();
$colours = appconf ('colours');
$defaultColour = 'transparent';

if ($parameters['highlight'] == 'no') {
	$colours = array ();
}

foreach ($users1 as $key => $value) {
	if (count ($colours) == 0) {
		$users[$value->name] = $defaultColour;
		continue;
	}
	$users[$value->name] = array_shift ($colours);
}

$dn->clear ();
$dn->orderBy ('ts desc');
$thread = $dn->find (
	array (
		'appname' => $appname,
	)
);
if (! $thread) {
	echo template_simple ('error.spt', $dn);
	if ($parameters['exit'] == 'yes') {
		exit;
	}
	return;
}

echo template_simple ('thread.spt', array ('users' => $users, 'inframe' => $inframe, 'thread' => $thread));

if ($parameters['exit'] == 'yes') {
	exit;
}

?>