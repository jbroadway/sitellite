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

loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

if ($box['context'] == 'action') {
	global $cgi;
	
	loader_import ('news.Story');
	loader_import ('news.Functions');
	
	$story = new NewsStory;
	
	$params = array ();
	
	if (! empty ($parameters['section'])) {
		$params['category'] = $parameters['section'];
	}
	
	$story->limit (5);
	$story->orderBy ('date desc, rank desc, id desc');
	
	$list = $story->find ($params);
	if (! $list) {
		$list = array ();
	}
//START: SEMIAS. #192 Test all config files for multilingual dates
    foreach($list as $item){
        $story->date = intl_date ($obj->date, 'shortcevdate');
    }
//END: SEMIAS.
//	page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');

	echo template_simple ('ticker.spt', array (
		'list' => $list,
		'bg' => $parameters['bg'],
		'width' => $parameters['width'],
		'border' => $parameters['border'],
	));
	exit;
} else {
	echo template_simple ('ticker_iframe.spt', $parameters);
}

?>