<?php

global $cgi;

if (empty ($cgi->collection)) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

loader_import ('cms.Versioning.Rex');
loader_import ('saf.GUI.Pager');
//loader_import ('saf.Misc.TableHeader');
//loader_import ('cms.Versioning.Facets');

$limit = 10;

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$data = array ('collection' => $cgi->collection);

$rex = new Rex ($cgi->collection);

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

// get access control
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
}

$pg = new Pager ($cgi->offset, $limit);
$pg->url = site_current () . '?collection=' . urlencode ($cgi->collection);

$res = $rex->getDeleted ($limit, $cgi->offset, $acl);
if (! $res) {
	$res = array ();
	$rex->total = 0;
}
$pg->total = $rex->total;

$res2 = array ();
foreach ($res as $k => $v) {
	$res2[$v->{$rex->key}] = $v;
}

function pretty_date ($date) {
	loader_import ('saf.Date');
	return Date::timestamp ($date, 'M j, Y - g:ia');
}

$pg->setData ($res2);
$pg->update ();

//page_title (intl_get ('Browsing') . ': ' . $rex->info['Collection']['display']);
$data['collection_name'] = $rex->info['Collection']['display'];
$data['title_field'] = $rex->info['Collection']['title_field'];

if (! session_allowed ('approved', 'w', 'status')) {
	$data['restore'] = false;
} else {
	$data['restore'] = true;
}














echo template_simple (CMS_JS_ALERT_MESSAGE, $GLOBALS['cgi']);

echo loader_box ('cms/nav');
















template_simple_register ('pager', $pg);
echo template_simple ('deleted_items.spt', $data);

?>