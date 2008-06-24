<?php

global $cgi;

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$q = db_query ('select * from siteinvoice_client order by name asc');

if (! $q->execute ()) {
	$total = 0;
	$clients = array ();
} else {
	$total = $q->rows ();
	$clients = $q->fetch ($cgi->offset, 20);
}
$q->free ();

loader_import ('saf.GUI.Pager');

$pg = new Pager ($cgi->offset, 20, $total);
$pg->setUrl (site_current () . '?');
$pg->update ();

page_title ('SiteInvoice - Clients (' . count ($clients) . ')');

echo template_simple ('nav.spt');

template_simple_register ('pager', $pg);

echo template_simple ('clients.spt', array ('clients' => $clients));

?>