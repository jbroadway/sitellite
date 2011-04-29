<?php

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

$messages = $msg->getNew ();
if (! is_array ($messages)) {
	//echo '<p>' . intl_get ('No new messages.') . '</p>';
	//return;
}

function msg_date_format ($date) {
	loader_import ('saf.Date');
	return Date::timestamp (
		$date,
		array (
			'today' => '\T\o\d\a\y - g:i A',
			'yesterday' => '\Y\e\s\t\e\r\d\a\y - g:i A',
			'tomorrow' => '\T\o\m\o\r\r\o\w - g:i A',
			'this week' => 'l, F j, Y - g:i A',
			'other' => 'F j, Y - g:i A',
		)
	);
}

function msg_get_name ($user) {
	$info = session_get_user ($user);
	if (! $info) {
		return $user;
	}
	if (! empty ($info->lastname)) {
		return $info->lastname . ', ' . $info->firstname;
	}
	return $user;
}

echo template_simple ('messages/inbox.spt', array ('messages' => $messages));

?>