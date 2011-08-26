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

page_title ('SiteShop 2 - ' . intl_get ('Products'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'products'));

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

if (! $parameters['orderBy']) {
	$parameters['orderBy'] = 'name';
}

if (! $parameters['sort']) {
	$parameters['sort'] = 'asc';
}

loader_import ('cms.Versioning.Rex');

$r = new Rex (false);
$r->preserve = array ('orderBy', 'sort');
$r->addFacet ('sku', array ('type' => 'text', 'display' => intl_get ('Text'), 'fields' => 'sku, name, description, body'));
$r->addFacet ('category', array ('type' => 'select', 'display' => intl_get ('Category'), 'values' => db_pairs ('select * from siteshop_category order by name asc'), 'count' => false));
$r->addFacet ('availability', array ('type' => 'select', 'display' => intl_get ('Availability'), 'values' => appconf ('availability'), 'count' => false));
$r->addFacet ('weight', array ('type' => 'select', 'display' => intl_get ('Sorting Weight'), 'values' => appconf ('weight'), 'count' => false));
$r->addFacet ('sitellite_status', array ('type' => 'select', 'display' => intl_get ('Status'), 'values' => assocify (session_get_statuses ()), 'count' => false));
$r->addFacet ('sitellite_access', array ('type' => 'select', 'display' => intl_get ('Access Level'), 'values' => assocify (session_get_access_levels ()), 'count' => false));
$parameters['facets'] = $r->renderFacets ();

$search_params = array ();
foreach ($parameters as $k => $v) {
	if ($k == '_category') {
		$list = db_shift_array ('select product_id from siteshop_product_category where category_id = ?', $v);
		$search_params[] = 'id in(' . join (', ', $list) . ')';
	} elseif ($k == '_sku') {
		$search_params[] = '(sku like "%' . $v . '%" or name like "%' . $v . '%" or description like "%' . $v . '%" or body like "%' . $v . '%")';
	} elseif (in_array ($k, array ('_availability', '_weight', '_sitellite_status', '_sitellite_access'))) {
		// exact matches
		$search_params[substr ($k, 1)] = $v;
	}
}

$p = new Product ();
$p->orderBy ($parameters['orderBy'] . ' ' . $parameters['sort']);
$p->offset ($parameters['offset']);
$p->limit (20);
$parameters['list'] = $p->find ($search_params);
$parameters['total'] = $p->total;

loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
$pg = new Pager ($parameters['offset'], 20, $parameters['total']);
$pg->setUrl (site_prefix () . '/index/siteshop-admin-products-action?');
$pg->getInfo ();
template_simple_register ('pager', $pg);
// END: SEMIAS
echo template_simple ('admin_products.spt', $parameters);

?>