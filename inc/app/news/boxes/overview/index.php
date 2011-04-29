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
// #192 Test all config files for multilingual dates.

loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');
if (!session_allowed ('private', 'r', 'access')) {
    $res = db_shift_array ("select distinct category from sitellite_news where sitellite_access = 'public' order by date desc limit 6");
} else {
    $res = db_shift_array ('select distinct category from sitellite_news order by date desc limit 6');
}

$list = array ();
$sub = array ();
foreach ($res as $key) {
	$res = db_fetch_array ('select * from sitellite_news where category = ? order by date desc limit 3', $key);
	$list[$key] = array_shift ($res);
	$sub[$key] = $res;
}

loader_import ('news.Functions');

page_title (intl_get ('Latest Articles'));

foreach ($list as $item) {
    $item->date = intl_date ($item->date, 'shortcevdate');
}

echo template_simple ('overview.spt', array ('list' => $list, 'sub' => $sub));

?>