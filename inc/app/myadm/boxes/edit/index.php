<?php

global $cgi;

if (empty ($cgi->_table)) {
	header ('Location: ' . site_prefix () . '/index/myadm-app');
	exit;
}

loader_import ('saf.MailForm');

$tbl = db_table ($cgi->_table);
$tbl->getInfo ();

$form = new MailForm;
$form->widgets = $tbl->columns;

$w =& $form->addWidget ('hidden', '_key');
$w =& $form->addWidget ('hidden', '_table');

$w =& $form->addWidget ('msubmit', 'submit_button');
$b1 =& $w->getButton ();
$b1->setValues ('Save');
$b2 =& $w->addButton ('cancel_button');
$b2->setValues ('Cancel');
$b2->extra = 'onclick="history.go (-1); return false"';

$original = $tbl->fetch ($cgi->_key);
foreach (get_object_vars ($original) as $key => $value) {
	$form->widgets[$key]->setValue ($value);
}

$form->error_mode = 'all';

if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	page_title ( 'Database Manager - Editing item "' . $cgi->_table . '/' . $cgi->_key . '"' );
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();
	unset ($vals['_table']);
	unset ($vals['_key']);
	unset ($vals['submit_button']);
	$res = $tbl->update ($cgi->_key, $vals);
	if (! $res) {
		die ($tbl->error);
	}
	page_title ( '<h1>Database Manager - Saved item' );
	echo template_simple ('<p><a href="{site/prefix}/index/myadm-browse-action?table=' . urlencode ($cgi->_table) . '">Back</a></p>');
}

//exit;

?>