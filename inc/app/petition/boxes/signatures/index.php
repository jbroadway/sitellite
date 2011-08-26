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

loader_import ('saf.GUI.Pager');

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

$limit = 100;

$signature = new Signature ();
$signature->orderBy ('ts asc');
$signature->limit ($limit);
$signature->offset ($parameters['offset']);
$parameters['list'] = $signature->find (array ('petition_id' => $parameters['id']));
$parameters['total'] = $signature->total;
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.	
$pg = new Pager ($parameters['offset'], $limit, $signature->total);
$pg->setUrl (site_prefix () . '/index/petition-signatures-action/id.%s?', $parameters['id']);
$pg->getInfo ();
template_simple_register ('pager', $pg);
// END: SEMIAS
foreach ($parameters['list'] as $k => $v) {
	$parameters['list'][$k]->num = ($k + 1) + ($parameters['offset']);
}

page_title (intl_get ('Signatures'));
echo template_simple ('signatures.spt', $parameters);

?>