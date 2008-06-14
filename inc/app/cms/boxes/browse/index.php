<?php

global $cgi;

if (empty ($cgi->collection)) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

loader_import ('cms.Versioning.Rex');
loader_import ('saf.GUI.Pager');
loader_import ('saf.Misc.TableHeader');
loader_import ('cms.Versioning.Facets');
loader_import ('cms.Workflow.Lock');

lock_init ();

$limit = session_pref ('browse_limit');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

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
$pg->url = site_current () . '?collection=' . urlencode ($cgi->collection) . '&orderBy=' . urlencode ($cgi->orderBy) . '&sort=' . urlencode ($cgi->sort);
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

/*
if (isset ($rex->info['Collection']['key_field_name'])) {
	$data['headers'][] = new TableHeader ($rex->info['Collection']['key_field'], $rex->info['Collection']['key_field_name']);
	if (isset ($rex->info['Collection']['key_field_align'])) {
		$data['key_field_align'] = $rex->info['Collection']['key_field_align'];
	}
}
$data['headers'][] = new TableHeader ($rex->info['Collection']['title_field'], $rex->info['Collection']['title_field_name']);

if (in_array ('sitellite_status', $struct)) {
	$data['headers'][] = new TableHeader ('sitellite_status', intl_get ('Status'));
}

if (in_array ('sitellite_access', $struct)) {
	$data['headers'][] = new TableHeader ('sitellite_access', intl_get ('Access'));
}
*/

// build facets

$rex->bookmark = true;

if (count ($rex->facets) > 0) {
	$data['facets'] = $rex->renderFacets ();
} else {
	$data['facets'] = '';
}
$data['is_versioned'] = $rex->isVersioned;

/*
$f = new rSelectFacet ('sitellite_status', intl_get ('Status'));
$f->setOptions (array ('draft' => intl_get ('Draft'), 'approved' => intl_get ('Approved')));
$data['facets'][] = $f;

$f = new rSelectFacet ('sitellite_access', intl_get ('Access Level'));
$f->setOptions (array ('public' => intl_get ('Public'), 'private' => intl_get ('Private')));
$data['facets'][] = $f;

$f = new rSelectFacet ('sitellite_owner', intl_get ('Owner'));
$f->setOptions (array ('admin' => intl_get ('Admin')));
$data['facets'][] = $f;
*/

// compile data

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
	$locks[$row->{$rex->key}] = lock_exists ($cgi->collection, $row->{$rex->key});
	if ($locks[$row->{$rex->key}]) {
		$lock_info = lock_info ($cgi->collection, $row->{$rex->key});
		$res2[$row->{$rex->key}]->lock_owner = $lock_info->user;
		$res2[$row->{$rex->key}]->lock_expires = $lock_info->expires;
		loader_import ('cms.Filters');
	}
	if (isset ($row->sitellite_access) && ! session_allowed ($row->sitellite_access, 'w', 'access')) {
		$editable[$row->{$rex->key}] = false;
	} elseif (isset ($row->sitellite_team) && ! session_allowed ($row->sitellite_team, 'w', 'team')) {
		$editable[$row->{$rex->key}] = false;
	} else {
		$editable[$row->{$rex->key}] = true;
	}
}

$pg->setData ($res2);
$pg->update ();

//page_title (intl_get ('Browsing') . ': ' . $rex->info['Collection']['display']);
$data['collection_name'] = intl_get ($rex->info['Collection']['display']);
$data['collection_singular'] = intl_get ($rex->info['Collection']['singular']);
$data['title_field'] = $rex->info['Collection']['title_field'];
$data['key_field'] = $rex->info['Collection']['key_field'];

if (session_is_resource ('delete') && ! session_allowed ('delete', 'rw', 'resource')) {
	$data['deletable'] = false;
} else {
	$data['deletable'] = true;
}

if (isset ($rex->info['Collection']['add']) && $rex->info['Collection']['add'] == false) {
	$data['add'] = false;
} elseif (session_allowed ('add', 'rw', 'resource')) {
	$data['add'] = true;
}

$data['links'] = array ();
foreach ($rex->info as $key => $vals) {
	if (strpos ($key, 'link:') === 0) {
		$perms = $vals['requires'];
		switch ($perms) {
			case 'r':
			case 'w':
			case 'rw':
				if (session_is_resource ($cgi->collection) && ! session_allowed ($cgi->collection, $perms, 'resource')) {
					continue;
				}
				break;
		}
		$vals['text'] = intl_get ($vals['text']);
		if (strpos ($vals['url'], '/index/') === 0) {
			$vals['url'] = site_prefix () . $vals['url'];
		}
		$data['links'][] = $vals;
	}
}












echo template_simple (CMS_JS_ALERT_MESSAGE, $GLOBALS['cgi']);

echo loader_box ('cms/nav');















template_simple_register ('pager', $pg);
template_simple_register ('locks', $locks);
template_simple_register ('editable', $editable);
template_simple_register ('align', $align);
template_simple_register ('width', $width);
echo template_simple ('browse.spt', $data);

//info ($pg);

?>
