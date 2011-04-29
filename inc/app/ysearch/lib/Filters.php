<?php

function ysearch_filter_size ($s) {
	if ($s == -1) {
		return 'Unlimited';
	}

	if ($s >= 1000000000) {
		return round ($s / 1000000000 * 10) / 10 . " GB";
	} elseif ($s >= 1000000) {
		return round ($s / 1000000 * 10) / 10 . " MB";
	} elseif ($s >= 1000) {
		return round ($s / 1000) . " KB";
	} else {
		return $s . " B";
	}
}

function ysearch_filter_title ($t) {
	foreach (appconf ('titles') as $title) {
		if (strpos ($t, $title) === 0) {
			$t = substr ($t, strlen ($title));
			if (strlen ($t) == 0) {
				return 'Untitled';
			}
			return $t;
		}
	}
	return $t;
}

?>