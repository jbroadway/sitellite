<?php

loader_import ('saf.Misc.RPC');
loader_import ('ui.Filters');

class CommentsRPC {

function add ($group, $item, $user, $comment, $approved) {
	db_execute ('INSERT INTO ui_comment SET
		user=?, item=?, `group`=?, comment=?, approved=?',
		$user, $item, $group, $comment, $approved);
	$result = db_single ('SELECT * FROM ui_comment WHERE id=?', db_lastid ());
	$result->author = '';
	
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

function approve ($id) {
	db_execute ('UPDATE ui_comment SET approved=1 WHERE id=?', $id);
	return db_error ();
}

}

echo rpc_handle (new CommentsRPC, $parameters);
exit;

?>
