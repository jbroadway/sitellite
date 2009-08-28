<?php

loader_import ('saf.Misc.RPC');
loader_import ('ui.Filters');

class CommentsRPC {

function add ($group, $item, $name, $email, $website, $comment, $approved) {

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


	$website = ui_website_filter ($website);
	$name = trim (strip_tags ($name));
	$comment = strip_tags ($comment);
	db_execute ('INSERT INTO ui_comment SET
		name=?, item=?, `group`=?, email=?, website=?, comment=?, approved=?, ip=?',
		$name, $item, $group, $email, $website, $comment, $approved, $_SERVER['REMOTE_ADDR']);
	$result = db_single ('SELECT * FROM ui_comment WHERE id=?', db_lastid ());
	$result->admin = session_admin ();
	if (! empty ($result->website)) {
		$result->name = '<a href="' . $result->website . '" rel="nofollow">' . $result->name . '</a>';
	}
	
	$conv = array (
		'<' => '%3C',
		'>' => '%3E',
		'"' => '%22');
	return strtr (template_simple ('comment-item.spt', $result), $conv);
}

function del ($id) {
	db_execute ('DELETE FROM ui_comment WHERE id=?', $id);
	return db_error ();
}

function ban ($id) {
	$res = db_single ('SELECT * FROM ui_comment WHERE id=?', $id);
	db_execute ('INSERT INTO sitellite_form_blacklist VALUES (?)', $res->ip);
	db_execute ('DELETE FROM ui_comment WHERE id=?', $id);
	return db_error ();
}

function approve ($id) {
	db_execute ('UPDATE ui_comment SET approved=1 WHERE id=?', $id);
	return db_error ();
}

}

echo rpc_handle (new CommentsRPC, $parameters);
exit;

?>
