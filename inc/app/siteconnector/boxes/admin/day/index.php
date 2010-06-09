<?php

page_title ('SiteConnector - Queries by Day');

loader_import ('siteconnector.Filters');
loader_import ('siteconnector.Logger');
loader_import ('saf.GUI.Pager');

// single day's queries

$logger = new SiteConnector_Logger;

global $cgi;

if (empty ($cgi->date)) {
	$cgi->date = date ('Y-m-d');
}

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$res = $logger->getQueries ($cgi->date, $cgi->offset, 20);
if (! is_array ($res)) {
	$res = array ();
}

$pg = new Pager ($cgi->offset, 20, $logger->total);
$pg->getInfo ();
$pg->setUrl (site_prefix () . '/index/siteconnector-admin-day-action?date=%s', $cgi->date);

template_simple_register ('pager', $pg);
echo template_simple ('admin_day.spt', array ('list' => $res));

?>