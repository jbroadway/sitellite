<?php

loader_import ('saf.Misc.RPC');
loader_import ('ui.Filters');
loader_import ('ui.Reviews');

class ReviewsRPC {

function add ($group, $item, $user, $comment, $rating, $approved, $nstars) {

	// Blacklist
	if (appconf ('blacklist')) {
		if (db_shift ('select count(*) from sitellite_form_blacklist where ip_address = ?', $_SERVER['REMOTE_ADDR'])) {
			die ('The IP address submitting this form has been blacklisted due to abuse.  If you feel this has been done in error, please contact the website owner.');
		}
	}

	// Verify session
	if (appconf ('verify_session')) {
		@session_start ();
		if ($_SESSION['mf_verify_session'] != 'mf_verified') {
			die ('This form requires that you enable cookies in your browser, which helps us to prevent abuse of our forms by automated spam systems.');
		}
	}
	
	// Verify sender
	if (appconf ('verify_sender')) {
		if (strtoupper ($_SERVER['REQUEST_METHOD']) != 'GET') {
			die ('Invalid request method!');
		}
		if (strpos ($_SERVER['HTTP_REFERER'], site_url ()) !== 0) {
			die ('Invalid referrer!');
		}
	}

	$comment = strip_tags ($comment);
	db_execute ('REPLACE INTO ui_review SET
		user=?, item=?, `group`=?, comment=?, approved=?',
		$user, $item, $group, $comment, $approved);
	db_execute ('REPLACE INTO ui_rating SET
		user=?, item=?, `group`=?, rating=?',
		$user, $item, $group, $rating);

	
	$result = db_single ('SELECT ui_review.*, ui_rating.rating,
			firstname, lastname, email, website
		FROM ui_review, ui_rating
		LEFT JOIN sitellite_user
			ON username = user
		WHERE ui_review.user=? AND ui_review.item=? AND
			ui_review.`group`=? AND
			ui_review.user = ui_rating.user AND
			ui_review.item = ui_rating.item AND
			ui_review.`group` = ui_rating.`group`',
			$user, $item, $group);
	$result->admin = session_admin ();
	$result->name = trim ($result->firstname . ' ' . $result->lastname);
	if (empty ($result->name)) {
		$result->name = $result->user;
	}
	if (! empty ($result->website)) {
		$result->name = '<a href="' . $result->website . '" rel="nofollow">' . $result->name . '</a>';
	}
	$result->ratingwidget = Reviews::getRatingWidget ($result->user,
		$result->rating, Reviews::getRatingValues ($nstars));
        $conv = array (
                '<' => '%3C',
                '>' => '%3E',
                '"' => '%22');
        return strtr (template_simple ('review-item.spt', $result), $conv);
}

function del ($user, $item, $group) {
	db_execute ('DELETE FROM ui_review WHERE user=? AND item=? and `group`=?',
		$user, $item, $group);
	db_execute ('DELETE FROM ui_rating WHERE user=? AND item=? and `group`=?',
		$user, $item, $group);
	return db_error ();
}

/*
function ban ($id) {
	$res = db_single ('SELECT * FROM ui_comment WHERE id=?', $id);
	db_execute ('INSERT INTO sitellite_form_blacklist VALUES (?)', $res->ip);
	db_execute ('DELETE FROM ui_comment WHERE id=?', $id);
	return db_error ();
}
*/

function approve ($user, $item, $group) {
	db_execute ('UPDATE ui_review SET approved=1
		WHERE item=?, user=?, `group`=?', $item, $user, $group);
	return db_error ();
}

}

echo rpc_handle (new ReviewsRPC, $parameters);
exit;

?>
