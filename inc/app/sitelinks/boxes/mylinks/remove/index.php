<?php

if (! appconf ('user_submissions')) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

if (! session_valid ()) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

loader_import ('cms.Versioning.Rex');

$rex = new Rex ('sitelinks_item');

$rex->delete ($parameters['id']);

loader_import ('saf.Database.PropertySet');

$ps = new PropertySet ('sitelinks_item', $parameters['id']);

$ps->delete ();

header ('Location: ' . site_prefix () . '/index/sitelinks-mylinks-action');
exit;

?>