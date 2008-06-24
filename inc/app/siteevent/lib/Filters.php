<?php

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

?>