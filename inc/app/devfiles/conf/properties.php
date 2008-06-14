<?php

// note: do NOT alter values below.  this script gets its
// settings from the devnotes_config database table.  instead,
// use the devnotes admin interface to change settings.

// get settings from the db
$res = db_single ('select * from devfiles_config');
if (! is_object ($res)) {
	// create a sane default
	$res = new StdClass;
	$res->id = 0;
	$res->notes = 'on';
	$res->contact = '';
	$res->ignore_list = 'admin';
	$res->allowed_types = '';
	$res->not_allowed = 'exe,vbs';
}

// on, off, or date
$res->files = ($res->files === 'on') ? true : $res->files;
$res->files = ($res->files === 'off') ? false : $res->files;
define ('DEVFILES', $res->files);

// send email to these foolios when a note is made
$res->contact = (empty ($res->contact)) ? false : $res->contact;
appconf_set ('contact', $res->contact);

// don't send emails for notes from these foolios
$res->ignore_list = preg_split ('/, ?/', $res->ignore_list);
appconf_set ('ignore', $res->ignore_list);

// only let people upload files of these types
$res->allowed_types = preg_split ('/, ?/', $res->allowed_types, -1, PREG_SPLIT_NO_EMPTY);
appconf_set ('allowed', $res->allowed_types);

// or at least make sure the files are not of these types
$res->not_allowed = preg_split ('/, ?/', $res->not_allowed, -1, PREG_SPLIT_NO_EMPTY);
appconf_set ('not_allowed', $res->not_allowed);

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