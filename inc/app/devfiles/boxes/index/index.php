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

/*if (isset ($parameters['file'])) {
	info ($parameters['file'], true);
	return;
}*/

global $page, $cgi;

if (isset ($cgi->appname)) {
	$appname = $cgi->appname;
	$inframe = true;
} else {
	$appname = $page->id;
	$inframe = false;
}

loader_import ('devfiles.Files');
$fl = new Files;

if (DEVFILES === true || (is_string (DEVFILES) && strtotime (DEVFILES) > time ())) {
	$_files = true;
} else {
	$_files = false;
}

if (! empty ($parameters['style'])) {
	echo '<link rel="stylesheet" type="text/css" href="' . $parameters['style'] . '" />';
}

// post file

if ($_files) {

if (is_object ($parameters['file'])) {
	if (empty ($parameters['name'])) {
		$parameters['name'] = $parameters['nameChooser'];
	}

	$name = $parameters['file']->name;
	if (! empty ($parameters['rename'])) {
		$info = pathinfo ($name);
		if (strstr ($parameters['rename'], $info['extension'])) {
			$name = $parameters['rename'];
		} else {
			$name = $parameters['rename'] . '.' . $info['extension'];
		}
	}

	$info = pathinfo ($name);
	if (! $fl->verifyType ($info['extension'])) {
		$fl->error = intl_get ('Invalid file format');
		echo '<h1>' . intl_get ('DevFiles') . '</h1>';
		echo template_simple ('error.spt', $fl);
		return;
	}

	$res = $fl->add (
		$parameters['file']->tmp_name, // temp file location
		$parameters['appname'],
		$parameters['name'], // user name
		$name, // new file name
		$parameters['file']->size // file size
	);
	if (! $res) {
		echo '<h1>' . intl_get ('DevFiles') . '</h1>';
		echo template_simple ('error.spt', $fl);
		return;
	}

	$contact = appconf ('contact');
	if ($contact && ! in_array ($parameters['name'], appconf ('ignore'))) {
		$parameters['_filename'] = $res;
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//		@mail ($contact, intl_get ('Files Notice'), template_simple ('email.spt', $parameters));
//-----------------------------------------------
        site_mail (
            $contact,
            intl_get ('Files Notice'),
            template_simple ('email.spt', $parameters),
            array ("Is_HTML" => true)
        );
//END: SEMIAS.
	}

	// on success, forward back to referring page
	if (isset ($cgi->appname)) {
		header ('Location: ' . $_SERVER['HTTP_REFERER'] . '#files');
		exit;
	} else {
		header ('Location: ' . $appname . '#files');
		exit;
	}
}

} // end if $_files

// display files

$fl->listFields ('distinct name');
$fl->orderBy ('ts asc');
$users1 = $fl->find (
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

$fl->clear ();
$fl->orderBy ('ts desc');
$files = $fl->find (
	array (
		'appname' => $appname,
	)
);
if (! $files) {

	if ($parameters['hideIfEmpty'] == 'yes' && ! session_admin ()) {
		return;
	} else {

		$title = 'DevFiles';
		if (isset ($parameters['title'])) {
			$title = $parameters['title'];
		}

		echo '<a name="files"></a><h1>' . intl_get ($title) . '</h1>';

		if (session_admin ()) {
			if ($inframe) {
				$target = ' target="_parent"';
			} else {
				$target = '';
			}
			echo template_simple ('<p><a href="{site/prefix}/index/devfiles-admin-action"' . $target . '>{intl DevFiles Admin}</a></p>');
		}

		echo template_simple ('error.spt', $fl);
	}

} else {

	$title = 'DevFiles';
	if (isset ($parameters['title'])) {
		$title = $parameters['title'];
	}

	echo '<a name="files"></a><h1>' . intl_get ($title) . '</h1>';

	if (session_admin ()) {
		if ($inframe) {
			$target = ' target="_parent"';
		} else {
			$target = '';
		}
		echo template_simple ('<p><a href="{site/prefix}/index/devfiles-admin-action"' . $target . '>{intl DevFiles Admin}</a></p>');
	}

	foreach ($files as $k => $v) {
		$files[$k]->path = site_prefix () . '/' . $fl->getPath ($v->file, $appname);
	}
	echo template_simple ('files.spt', array ('users' => $users, 'files' => $files, 'showFileInfo' => $parameters['showFileInfo'], 'appname' => $appname));
}

if (isset ($parameters['posting']) && ! session_allowed ($parameters['posting'], 'rw', 'access')) {
	if ($parameters['exit'] == 'yes') {
		exit;
	}
	return;
}

if ($_files) {

// display add form

$fl->clear ();
$fl->listFields ('distinct name');
$fl->orderBy ('ts asc');
$users1 = $fl->find (array ());
if (! is_array ($users1)) {
	$users1 = array ();
}
$users = array ();
foreach ($users1 as $key => $value) {
	$users[] = $value->name;
}

echo template_simple ('form.spt', array ('users' => $users, 'appname' => $appname));

} // end if $_files

if ($parameters['exit'] == 'yes') {
	exit;
}

?>