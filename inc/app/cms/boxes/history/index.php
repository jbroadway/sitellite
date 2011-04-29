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

loader_import ('saf.GUI.Pager');
loader_import ('cms.Versioning.Rex');

if (! isset ($cgi->_return)) {
	if (strstr ($_SERVER['HTTP_REFERER'], 'cms-edit-form')) {
		$cgi->_return = site_prefix () . '/index/cms-edit-form?_key=' . urlencode ($cgi->_key) . '&_collection=' . urlencode ($cgi->_collection);
	} else {
		$cgi->_return = site_prefix () . '/index/' . urlencode ($cgi->_key);
	}
}

$limit = session_pref ('browse_limit');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$rex = new Rex ($cgi->_collection); // default: database, database

if (! $rex->collection) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

$pg = new Pager ($cgi->offset, $limit);

$history = $rex->getHistory ($cgi->_key, false, $limit, $cgi->offset);
if (! $history) {
	$history = array ();
	//die ($rex->error);
}

$pg->total = $rex->total;
$pg->setData ($history);
$pg->update ();

function pretty_date ($date) {
	loader_import ('saf.Date');
	return Date::timestamp ($date, 'M j, Y - g:ia');
}

$cur = $rex->getCurrent ($cgi->_key);
if (! $cur) {
	$title = $cgi->_key;
} else {
	$title = $cur->{$rex->info['Collection']['title_field']};
}

if (! session_allowed ('approved', 'w', 'status')) { //isset ($cur->sitellite_access) && ! session_allowed ($cur->sitellite_access, 'w', 'access')) {
	$editable = false;
} elseif (isset ($cur->sitellite_access) && ! session_allowed ($cur->sitellite_access, 'w', 'access')) {
	$editable = false;
} elseif (isset ($cur->sitellite_status) && ! session_allowed ($cur->sitellite_status, 'w', 'status')) {
	$editable = false;
} elseif (isset ($cur->sitellite_team) && ! session_allowed ($cur->sitellite_team, 'w', 'team')) {
	$editable = false;
} else {
	$editable = true;
}

if ($cgi->offset == 0) {
	$cgi->_current = $history[0]->sv_autoid;
}

$pg->url = site_current () . '?_collection=' . urlencode ($cgi->_collection) . '&_key=' . urlencode ($cgi->_key) . '&_return=' . urlencode ($cgi->_return) . '&_current=' . urlencode ($cgi->_current);

page_title (intl_get ('Change History') . ': ' . $rex->info['Collection']['display'] . ' / ' . $title);

// Start: SEMIAS #177 Pagination.

$pg->getInfo ();
// Check on ? 
$pos = strpos ( $_SERVER['REQUEST_URI'] , "?" );
if($pos === false) {
    $url = $_SERVER['REQUEST_URI'] . "?";
} else {
    $url = $_SERVER['REQUEST_URI'];
}
$pg->setUrl (preg_replace ('/&offset=[0-9]+/', '', $url));

// END: SEMIAS

template_simple_register ('pager', $pg);
echo template_simple ('history.spt', array ('history' => $history, 'current' => $cgi->_current, 'editable' => $editable));

?>