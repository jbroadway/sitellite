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
// #177 Pagination.
//

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
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
$pg = new Pager ($cgi->offset, 20, $total);
$pg->setUrl (site_current () . '?');
$pg->update ();
// END: SEMIAS
page_title ('SiteInvoice - Clients (' . count ($clients) . ')');

echo template_simple ('nav.spt');

template_simple_register ('pager', $pg);

echo template_simple ('clients.spt', array ('clients' => $clients));

?>