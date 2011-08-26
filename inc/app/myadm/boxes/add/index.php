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

$w =& $form->addWidget ('hidden', '_table');

$w =& $form->addWidget ('msubmit', 'submit_button');
$b1 =& $w->getButton ();
$b1->setValues ('Add');
$b2 =& $w->addButton ('cancel_button');
$b2->setValues ('Cancel');
$b2->extra = 'onclick="history.go (-1); return false"';

$form->error_mode = 'all';

if ($form->invalid ($cgi)) {
	$form->setValues ($cgi);
	page_title ( 'Database Manager - Adding item to "' . $cgi->_table . '"' );
	echo $form->show ();
} else {
	$form->setValues ($cgi);
	$vals = $form->getValues ();
	unset ($vals['_table']);
	unset ($vals['submit_button']);
	unset ($vals['cancel_button']);
	$res = $tbl->insert ($vals);
	if (! $res && ! empty ($tbl->error)) {
		die ($tbl->error);
	}
	page_title ( 'Database Manager - Added item' );
	echo template_simple ('<p><a href="{site/prefix}/index/myadm-browse-action?table=' . urlencode ($cgi->_table) . '">Back</a></p>');
}

//exit;

?>