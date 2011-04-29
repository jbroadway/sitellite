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

loader_import ('sitemailer2.Filters');
loader_import ('cms.Versioning.Rex');
loader_import ('saf.GUI.Pager');
loader_import ('cms.Versioning.Facets');

$limit = session_pref ('browse_limit');

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

page_title ('SiteMailer 2');

$data = array ();

$clause = ' where ';
$bind = array ();

if (! empty ($cgi->_newsletter)) {
    $message_ids = db_shift_array ('select message from sitemailer2_message_newsletter where newsletter = ?', $cgi->_newsletter);

	if (count ($message_ids) > 0) {
	    $message_ids = 'in(' . implode (', ', $message_ids) . ')';
    
	    $sql .= $clause . ' id ' . $message_ids;
		$clause = ' and ';
	}

	$bind[] = $cgi->_newsletter;
}

if (! empty ($cgi->status)) {
	$sql .= $clause . 'status = ?';
	$bind[] = $cgi->status;
	$clause = ' and ';
} else {
	$sql .= $clause . 'status in("running", "done")';
	$clause = ' and ';
}


$total = db_shift ('select count(*) from sitemailer2_message ' . $sql);



$q = db_query ('select * from sitemailer2_message ' . $sql . ' order by date desc');

 
if ($q->execute ($bind)) {
	$res = $q->fetch ($cgi->offset, $limit);
	$q->free ();
    

} else {
    die ($q->error ());
}

foreach (array_keys ($res) as $k) {
	$res[$k]->bounced = 0;
	if ($res[$k]->status != 'done') {
		$res[$k]->numsent .= ' (' . round (($res[$k]->numsent / ($res[$k]->numsent + db_shift ('select count(*) from sitemailer2_q where message = ?', $res[$k]->id))) * 100) . '%)';
	}
}

$data['list'] =& $res;

$rex = new Rex (false);

$rex->facets['subject'] = new rTextFacet ('subject', array ('display' => intl_get ('Text'), 'type' => 'text'));
$rex->facets['subject']->preserve = array ('offset');
$rex->facets['subject']->fields = array ('id', 'subject', 'mbody');

$rex->facets['newsletter'] = new rSelectFacet ('newsletter', array ('display' => intl_get ('Newsletter'), 'type' => 'select'));
$rex->facets['newsletter']->preserve = array ('offset');
$rex->facets['newsletter']->options = db_pairs ('select id, name from sitemailer2_newsletter order by name asc');
$rex->facets['newsletter']->count = false;

$rex->facets['status'] = new rSelectFacet ('status', array ('display' => intl_get ('Status'), 'type' => 'select'));
$rex->facets['status']->preserve = array ('offset');
$rex->facets['status']->options = array ('running' => 'Running', 'done' => 'Done');
$rex->facets['status']->count = false;

$data['facets'] = $rex->renderFacets ();

// Start: SEMIAS #177 Pagination.
// ------------------
//$pg = new Pager ($cgi->offset, $limit, $total);
//$pg->url = site_prefix () . '/index/sitemailer2-archive-action?_newsletter=' . urlencode ($cgi->_newsletter) . '&_status=' . urlencode ($cgi->_status) . '&_subject=' . urlencode ($cgi->_subject);
//$pg->setData ($res);
//$pg->update ();
// ------------------

$pg = new Pager ($cgi->offset, $limit, $total);
$pg->url = site_prefix () . '/index/sitemailer2-archive-action?_newsletter=' . urlencode ($cgi->_newsletter) . '&_status=' . urlencode ($cgi->_status) . '&_subject=' . urlencode ($cgi->_subject);
$pg->getInfo ();
// Check on ? 
$pos = strpos ( $_SERVER['REQUEST_URI'] , "?" );
if($pos === false) {
    $url = $_SERVER['REQUEST_URI'] . "?";
} else {
    $url = $_SERVER['REQUEST_URI'];
}
$pg->setUrl (preg_replace ('/&offset=[0-9]+/', '', $url));
$pg->setData ($res);
$pg->update ();
template_simple_register ('pager', $pg);

// END: SEMIAS

echo template_simple ('archive.spt', $data);

?>