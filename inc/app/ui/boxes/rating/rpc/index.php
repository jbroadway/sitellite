<?php

loader_import ('saf.Misc.RPC');

class StarRating {

function setRating ($group, $item, $user, $rating) {
	db_execute ('REPLACE INTO ui_rating SET
		`group`=?, item=?, user=?, rating=?',
		$group, $item, $user, (int)$rating);
	return db_error ();
}

function setAndShow ($group, $item, $user, $rating) {
	$this->setRating ($group, $item, $user, $rating);
	return round (db_shift ('SELECT AVG(rating) FROM ui_rating
		WHERE `group`=? AND item=? GROUP BY item',
		$group, $item));
}

}

echo rpc_handle (new StarRating, $parameters);
exit;

?>
