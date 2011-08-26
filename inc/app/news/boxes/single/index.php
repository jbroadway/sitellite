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
// #192 Test all config files for multilingual dates
//

loader_import ('news.Story');
loader_import ('news.Functions');
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

$s = new NewsStory ();
$story = $s->get ($parameters['story']);
$story->show_thumb = $parameters['thumb'];
$story->show_date = $parameters['date'];
$story->show_summary = $parameters['summary'];

//START: SEMIAS. #192 Test all config files for multilingual dates
$story->date = intl_date ($obj->date, 'shortcevdate');
//END: SEMIAS.

echo template_simple ('sidebar_single.spt', $story);

?>