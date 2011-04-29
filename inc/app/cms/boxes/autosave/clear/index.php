<?php

loader_import ('saf.MailForm.Autosave');
$a = new Autosave ();

if (! empty ($parameters['url'])) {
	$a->clear ($parameters['url']);
} else {
	$a->clear_all ();
}

if (! empty ($parameters['forward'])) {
	header ('Location: ' . $parameters['forward']);
	exit;
}

header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
exit;

?>