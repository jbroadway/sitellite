<?php

loader_import ('saf.Misc.RPC');

class StarRating {

function setRating ($group, $item, $user, $rating) {
	db_execute ('REPLACE INTO ui_rating SET
		`group`=?, item=?, user=?, rating=?',
		$group, $item, $user, (int)$rating);
/*
        $curvals = db_single ('SELECT AVG(rating) AS avgrating,
                        COUNT(rating) AS nvotes FROM ui_rating
                        WHERE `group`=? AND item=? GROUP BY item',
                        $group, $item);
	$curvals->text = intl_get ('Thanks for rating!');
        switch ($curvals->nvotes) {
                case 0:
                        break;
                case 1:
                        $curvals->nvotes= intl_get ('1 rating.');
                        break;
                default:
                        $curvals->nvotes = intl_get ('{nvotes} ratings.', $curvals);
                        break;
        }
	$curvals->avgrating = round ($curvals->avgrating);
*/	
	$curvals->text = intl_get ('Thanks for rating!');
	$curvals->already = intl_get ('You have already rated.');

	return $curvals;
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
