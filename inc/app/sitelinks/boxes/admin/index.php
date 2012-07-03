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

// your admin UI begins here

page_title (intl_get ('SiteLinks'));

loader_import ('sitelinks.Functions');
loader_import ('sitelinks.Filters');
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

global $cgi;

$data = new StdClass;

if (empty ($cgi->top_range)) {
	$cgi->top_range = 'month';
}
$data->top_range = $cgi->top_range;

if (empty ($cgi->top_date)) {
	$cgi->top_date = date ('Y-m-d');
}
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//$data->top_date = $cgi->top_date;
//
//list ($start, $end) = sitelinks_get_range ($cgi->top_range, $cgi->top_date);
//$data->top_start = $start;
//$data->top_end = $end;
//list ($prev, $next) = sitelinks_get_dates ($cgi->top_range, $cgi->top_date);
//-----------------------------------------------

//28-06-2012:
//Arend: Bugfix in datedisplay (part 1) This bug creeped into some corners
//resulting in non-displaying lists.
//This is the old version:

	//$data->top_date = intl_date ($cgi->top_date, 'shortcevdate');

//End Bugfix (part 1)

//28-06-2012:
//Arend: Bugfix in datedisplay (part 2) This bug creeped into some corners
//resulting in non-displaying lists.
//This is the old version:

	//	list ($start, $end) = sitelinks_get_range ($cgi->top_range, intl_date ($cgi->top_date, 'shortcevdate')); //$cgi->top_date);
	//	$data->top_start = $start;
	//	$data->top_end = $end;
	//	list ($prev, $next) = sitelinks_get_dates ($cgi->top_range, intl_date ($cgi->top_date, 'shortcevdate')); //$cgi->top_date);
	//	//END: SEMIAS.
	//	$data->top_prev = $prev;
	//	$data->top_next = $next;

//	This it the correction:

	list ($start, $end) = sitelinks_get_range ($cgi->top_range, $cgi->top_date);
	$data->top_start = $start;
	$data->top_end = $end;
	list ($prev, $next) = sitelinks_get_dates ($cgi->top_range, $cgi->top_date);
	$data->top_prev = $prev;
	$data->top_next = $next;

//End Bugfix (part 2)


//28-06-2012:
//Arend: Bugfix in datedisplay (part 3) This bug creeped into some corners
//resulting in non-displaying lists.
//Added the 'week' option.
// date translations
if (($data->top_range) == 'day') {
    $data->display_top_date = intl_date ($cgi->top_date, 'cevdate');
    $data->display_top_prev = intl_date ($data->top_prev, 'cevdate');
    $data->display_top_next = intl_date ($data->top_next, 'cevdate');
} else if ($data->top_range == 'week') {
    $data->display_top_date = intl_date ($cgi->top_date, 'week');
    $data->display_top_prev = intl_date ($data->top_prev, 'week');
    $data->display_top_next = intl_date ($data->top_next, 'week');
} else if ($data->top_range == 'month') {
    $data->display_top_date = intl_date ($cgi->top_date, 'month');
    $data->display_top_prev = intl_date ($data->top_prev, 'month');
    $data->display_top_next = intl_date ($data->top_next, 'month');
} else if ($data->top_range == 'year') {
    $data->display_top_date = intl_date ($cgi->top_date, 'year');
    $data->display_top_prev = intl_date ($data->top_prev, 'year');
    $data->display_top_next = intl_date ($data->top_next, 'year');
}
//End bugfix (part 3)


// views
$list = db_fetch_array (
	'select item_id, count(*) as total from sitelinks_view where ts >= ? and ts <= ? group by item_id order by total desc limit 10',
	$start,
	$end
);

$data->views = $list;

// hits
$list = db_fetch_array (
	'select item_id, count(*) as total from sitelinks_hit where ts >= ? and ts <= ? group by item_id order by total desc limit 10',
	$start,
	$end
);

$data->hits = $list;

// ratings
$list = db_fetch_array (
	'select item_id, avg(rating) as rating, count(*) as votes from sitelinks_rating where ts >= ? and ts <= ? group by item_id order by rating desc limit 10',
	$start,
	$end
);

foreach (array_keys ($list) as $k) {
	$list[$k]->rating = number_format ($list[$k]->rating, 2);
}

$data->ratings = $list;

$data->drafts = db_shift ('select count(*) from sitelinks_item where sitellite_status = "draft"');

echo template_simple ('admin.spt', $data);

?>