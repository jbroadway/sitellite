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

loader_import ('cms.Versioning.Rex');
loader_import ('saf.GUI.Pager');
loader_import ('saf.Misc.TableHeader');
loader_import ('cms.Versioning.Facets');

$limit = session_pref ('browse_limit');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$cgi->collection = 'sitemailer2_recipient';

$data = array ('collection' => $cgi->collection);

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

if (session_is_resource ($cgi->collection) && ! session_allowed ($cgi->collection, 'r', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

if (! isset ($cgi->orderBy)) {
	if (isset ($rex->info['Collection']['order_by'])) {
		$cgi->orderBy = $rex->info['Collection']['order_by'];
	} else {
		$cgi->orderBy = $rex->info['Collection']['title_field'];
	}
}

if (! isset ($cgi->sort)) {
	if (isset ($rex->info['Collection']['sorting_order'])) {
		$cgi->sort = $rex->info['Collection']['sorting_order'];
	} else {
		$cgi->sort = 'asc';
	}
}

$pg = new Pager ($cgi->offset, $limit);
// Start: SEMIAS #177 Pagination.
// 	No fix had to be applied here, the ? is hardcoded in the url.
$pg->url = site_current () . '?collection=' . urlencode ($cgi->collection) . '&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort);
// END: SEMIAS
$data['facet_url'] = '';
foreach ($cgi->param as $p) {
	if (strpos ($p, '_') === 0 && $p != '_msg') {
		$data['facet_url'] .= '&' . $p . '=' . urlencode ($cgi->{$p});
	}
}
$pg->url .= $data['facet_url'];
$data['return'] = $pg->url;

// build column headers

$struct = array_keys ($rex->getStruct ());

$acl = array ();
if (! $struct) {
	$struct = array ();
} else {
	$acl_list = session_allowed_access_list ();
	if (! in_array ('all', $acl_list)) {
		foreach ($struct as $k) {
			if ($k == 'sitellite_access') {
				$acl = array (
					'sitellite_access' => new rList (
						'sitellite_access',
						session_allowed_access_list ()
					)
				);
				break;
			}
		}
	}
	$team_list = session_allowed_teams_list ();
	if (! in_array ('all', $team_list)) {
		$team_list[] = '';
		foreach ($struct as $k) {
			if ($k == 'sitellite_team') {
				$acl['sitellite_team'] = new rList (
					'sitellite_team',
					$team_list
				);
				break;
			}
		}
	}
}

foreach ($rex->info as $key => $vals) {
	if (strpos ($key, 'browse:') === 0) {
		$key = substr ($key, 7);
		$data['headers'][] = new TableHeader ($key, $vals['header']);
		$data['fields'][$key] = $vals;
		if (isset ($vals['filter_import'])) {
			loader_import ($vals['filter_import']);
		}
	}
}

if ($rex->isVersioned) {
	$res = $rex->getStoreList ($acl, $limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
} else {
	$res = $rex->getList ($acl, $limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
}

if (! $res) {
	$res = array ();
	$rex->total = 0;
}
$pg->total = $rex->total;

$rex->ignore (array ('_msg'));

if (count ($rex->facets) > 0) {
	$data['facets'] = $rex->renderFacets ();
} else {
	$data['facets'] = '';
}
$data['is_versioned'] = $rex->isVersioned;

$res2 = array ();
$locks = array ();
$editable = array ();
$align = array ();
$width = array ();

foreach ($res as $key => $row) {
	$row = $rex->getCurrent ($row->{$rex->key});
	if (empty ($row->{$rex->info['Collection']['title_field']})) {
		$row->{$rex->info['Collection']['title_field']} = $row->{$rex->key};
	}
	$tmp = new StdClass;
	foreach ($data['headers'] as $field) {
		$tmp->{$field->name} = $row->{$field->name};
		if ($data['fields'][$field->name]['filter']) {
			$filter = $data['fields'][$field->name]['filter'];
			$tmp->{$field->name} = $filter ($tmp->{$field->name});
		} elseif ($data['fields'][$field->name]['virtual']) {
			$virtual = $data['fields'][$field->name]['virtual'];
			$tmp->{$field->name} = $virtual ($row);
		} else {
			$tmp->{$field->name} = htmlentities_compat ($tmp->{$field->name});
		}
		$align[$field->name] = ($data['fields'][$field->name]['align']) ? $data['fields'][$field->name]['align'] : 'left';
		$width[$field->name] = ($data['fields'][$field->name]['width']) ? $data['fields'][$field->name]['width'] : 'auto';
		if (isset ($rex->info['browse:' . $field->name]['length']) && strlen ($tmp->{$field->name}) > $rex->info['browse:' . $field->name]['length']) {
			$tmp->{$field->name} = rtrim (substr ($tmp->{$field->name}, 0, $rex->info['browse:' . $field->name]['length'] - 3)) . '...';
		}
	}
    
	$res2[$row->{$rex->key}] = $tmp;
	//$locks[$row->{$rex->key}] = lock_exists ($cgi->collection, $row->{$rex->key});
	if (isset ($row->sitellite_access) && ! session_allowed ($row->sitellite_access, 'w', 'access')) {
		$editable[$row->{$rex->key}] = false;
	} else {
		$editable[$row->{$rex->key}] = true;
	}
}

$pg->setData ($res2);
$pg->update ();

//page_title (intl_get ('Browsing') . ': ' . $rex->info['Collection']['display']);
$data['collection_name'] = $rex->info['Collection']['display'];
$data['title_field'] = $rex->info['Collection']['title_field'];
$data['key_field'] = $rex->info['Collection']['key_field'];

$msg_list = appconf ('msg');

if (isset ($msg_list[$cgi->_msg])) {
	page_onload ('alert (\'' . $msg_list[$cgi->_msg] . '\')');
}

page_title ('SiteMailer 2');

template_simple_register ('pager', $pg);
template_simple_register ('editable', $editable);
template_simple_register ('align', $align);
template_simple_register ('width', $width);

echo template_simple ('subscribers.spt', $data);

?>