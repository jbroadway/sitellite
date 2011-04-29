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

page_title ('SiteShop 2 - ' . intl_get ('Orders'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'orders'));

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

if (! $parameters['orderBy']) {
	$parameters['orderBy'] = 'ts';
}

if (! $parameters['sort']) {
	$parameters['sort'] = 'desc';
}

loader_import ('cms.Versioning.Rex');

$r = new Rex (false);
$r->preserve = array ('orderBy', 'sort');
$r->addFacet ('id', array ('type' => 'text', 'display' => intl_get ('Text'), 'fields' => 'id, name, description, body'));
$r->addFacet ('status', array ('type' => 'select', 'display' => intl_get ('Status'), 'values' => array ('new' => intl_get ('New'), 'partly-shipped' => intl_get ('Partly-Shipped'), 'shipped' => intl_get ('Shipped'), 'cancelled' => intl_get ('Cancelled')), 'count' => false));
$parameters['facets'] = $r->renderFacets ();

$search_params = array ();
foreach ($parameters as $k => $v) {
	if ($k == '_id') {
		$search_params[] = '(id = "' . $v . '" or ship_to like "%' . $v . '%" or bill_to like "%' . $v . '%")';
	} elseif ($k == '_status') {
		// exact matches
		$search_params[substr ($k, 1)] = $v;
	}
}

$o = new Order ();
$o->orderBy ($parameters['orderBy'] . ' ' . $parameters['sort']);
$o->offset ($parameters['offset']);
$o->limit (20);
$parameters['list'] = $o->find ($search_params);
$parameters['total'] = $o->total;

loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
// 	Not sure a fix is needed here.
$pg = new Pager ($parameters['offset'], 20, $parameters['total']);
$pg->setUrl (site_prefix () . '/index/siteshop-admin-orders-action?');
$pg->getInfo ();
template_simple_register ('pager', $pg);
// END: SEMIAS

echo template_simple ('admin_orders.spt', $parameters);

?>