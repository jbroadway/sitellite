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
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
$pg = new Pager ($cgi->offset, 20, $logger->total);
$pg->getInfo ();
$pg->setUrl (site_prefix () . '/index/siteconnector-admin-day-action?date=%s', $cgi->date);
// END: SEMIAS
template_simple_register ('pager', $pg);
echo template_simple ('admin_day.spt', array ('list' => $res));

?>