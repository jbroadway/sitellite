<?php

loader_import ('saf.Misc.RPC');

class StarRating {

function setRating ($group, $item, $user, $rating) {
	db_execute ('REPLACE INTO ui_rating SET
		`group`=?, item=?, user=?, rating=?',
		$group, $item, $user, (int)$rating);
	$result = db_error ();
	if ($result) {
		return $result;
	}
	else {
		return intl_get ('Thanks for rating!');
	}
}

function unsetRating ($group, $item, $user) {
	db_execute ('DELETE FROM ui_rating WHERE
		`group`=? AND item=? AND user=?',
		$group, $item, $user);
	$result = db_error ();
	if ($result) {
		return $result;
	}
	else {
		return intl_get ('Vote canceled.');
	}
}

}

echo rpc_handle (new StarRating, $parameters);
exit;

?>
