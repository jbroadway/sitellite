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

}

echo rpc_handle (new StarRating, $parameters);
exit;

?>
