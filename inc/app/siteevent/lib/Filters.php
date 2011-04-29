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

function siteevent_filter_username ($user) {
	$info = session_get_user ($user);
	if (! $info) {
		return $user;
	}
	if (! empty ($info->lastname)) {
		return $info->lastname . ', ' . $info->firstname;
	}
	return $user;
}

function siteevent_filter_details ($details) {
	return xmlentities (strip_tags ($details));
}

function siteevent_filter_link_title ($t) {
	return strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$t
		)
	);
}

function siteevent_filter_day ($d) {
	list ($y, $m, $d) = explode ('-', $d);
	return (int) $d;
}

function siteevent_filter_audience ($audience) {
	if (strpos ($audience, ',') !== false) {
		$ids = preg_split ('/, ?/', $audience);
	} else {
		$ids = array ($audience);
	}

	$audiences = db_pairs ('select * from siteevent_audience');

	$o = '';
	$s = '';
	foreach ($ids as $aud) {
		$o .= $s . $audiences[$aud];
		$s = ', ';
	}
	return $o;
}

function &siteevent_translate (&$obj) {
	loader_import ('saf.Database.Generic');
	$g = new Generic ('siteevent_event', 'id');
	$g->multilingual = true;
	$res =& $g->translate ($obj);
	return $res;
}
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
/*
function siteevent_virtual_date ($vals) {
	if ($vals->until_date > $vals->date) {
		if (substr ($vals->date, 0, 4) != substr ($vals->until_date, 0, 4)) {
			// separate year
			$out = localdate ('M j, Y', strtotime ($vals->date));
			$out .= ' &ndash; ';
			$out .= localdate ('M j, Y', strtotime ($vals->until_date));
		} else {
			// same year
			$out = localdate ('M j', strtotime ($vals->date));
			$out .= ' &ndash; ';
			$out .= localdate ('M j, Y', strtotime ($vals->until_date));
		}
		return $out;
	}
	return localdate ('M j, Y', strtotime ($vals->date));
}

function siteevent_virtual_recurring ($vals) {
	if ($vals->until_date > $vals->date) {
		if ($vals->recurring == 'no') {
			return intl_get ('Daily');
		}
		return ucfirst ($vals->recurring);
	}
	return intl_get ('No');
}
*/
//-----------------------------------------------
function siteevent_virtual_date ($vals) {
	if ($vals->until_date > $vals->date) {
		if (substr ($vals->date, 0, 4) != substr ($vals->until_date, 0, 4)) {
			// separate year
			$out = intl_date ($vals->date, 'evdate');
			$out .= ' &ndash; ';
			$out .= intl_date ($vals->until_date, 'evdate');
		} else {
			// same year
			$out = intl_date ($vals->date, 'shortevdate');
			$out .= ' &ndash; ';
			$out .= intl_date ($vals->until_date, 'evdate');
		}
		return $out;
	}
	return intl_date ($vals->date);
}

function siteevent_virtual_recurring ($vals) {
	/*
	if ($vals->until_date > $vals->date) {
		if ($vals->recurring == 'no') {
			return intl_get ('Daily');
		}
		return ucfirst ($vals->recurring);
	}
	return intl_get ('No');
	*/
	return intl_get (ucfirst ($vals->recurring));
}
//END: SEMIAS.
?>