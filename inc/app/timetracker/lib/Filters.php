<?php

function timetracker_filter_username ($user) {
	$info = db_single ('select firstname, lastname from sitellite_user where username = ?', $user);
	if (! empty ($info->lastname)) {
		$out = $info->lastname;
		if (! empty ($info->firstname)) {
			$out .= ', ' . $info->firstname;
		}
	} else {
		$out = $user;
	}
	return $out;
}

?>