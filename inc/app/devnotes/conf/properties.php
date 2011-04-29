<?php

// note: do NOT alter values below.  this script gets its
// settings from the devnotes_config database table.  instead,
// use the devnotes admin interface to change settings.

// get settings from the db
$res = db_single ('select * from devnotes_config');
if (! is_object ($res)) {
	// create a sane default
	$res = new StdClass;
	$res->id = 0;
	$res->notes = 'on';
	$res->contact = '';
	$res->ignore_list = 'admin';
}

// on, off, or date
$res->notes = ($res->notes === 'on') ? true : $res->notes;
$res->notes = ($res->notes === 'off') ? false : $res->notes;
define ('DEVNOTES', $res->notes);

// send email to these foolios when a note is made
$res->contact = (empty ($res->contact)) ? false : $res->contact;
appconf_set ('contact', $res->contact);

// don't send emails for notes from these foolios
$res->ignore_list = preg_split ('/, ?/', $res->ignore_list);
appconf_set ('ignore', $res->ignore_list);

// list of alternating note highlighting colours
appconf_set (
	'colours',
	array (
		'#cde',
		'#cfc',
		'#fcc',
		'#ffc',
		'#cff',
		'#fcf',
		'#ccf',
		'#edc',
		'#ace',
		'#afa',
		'#faa',
		'#ffa',
		'#aff',
		'#faf',
		'#aaf',
		'#eee',
		'#ccc',
		'#aaa',
	)
);
//

?>