<?php

global $cgi;

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

if ($cgi->item) {
	$cgi->id = $cgi->item;
}

if ($cgi->category == 'Sent') {
	$res = $msg->getSent ($cgi->id);
} else {
	$res = $msg->get ($cgi->id);
}

if (! $res) {
	page_title (intl_get ('Reading Message'));
	echo '<p>Error: ' . $msg->error . '</p>';
	return;
}

if ($cgi->category == 'Sent') {
	$res->category = 'Sent';
} elseif ($cgi->category == 'Trash') {
	$res->category = 'Trash';
} elseif ($res->category == '') {
	$res->category = 'Inbox';
}

page_title (intl_get ('Reading Message') . ': ' . $res->subject);

$res->response_subject = Workspace::createResponseSubject ($res->subject);

$list = $msg->getRecipients ($cgi->id);

if (! $list) {
	$user_list = array ();
	$recipients = array (session_username ());
} else {
	$user_list = array ();
	$recipients = array ();
	foreach ($list as $user) {
		if ($user->user != $res->from_user) {
			$recipients[] = $user->user;
		}
		if ($user->user == session_username ()) {
			continue;
		}
		if ($user->type == 'email') {
			$user_list[] = $user->email;
		} else {
			$user_list[] = $user->user;
		}
	}
}

$res->user_list = array_unique ($user_list);
$res->recipients = array_unique ($recipients);

function msg_show_user ($user) {
	if (strstr ($user, '@')) {
		return '<a href="mailto:' . $user . '">' . $user . '</a>';
	}
	$info = session_get_user ($user);
	if (! $info || empty ($info->lastname)) {
		return '<a href="' . site_prefix () . '/index/cms-user-view-action?user=' . $user . '">' . $user . '</a>';
	}
	return '<a href="' . site_prefix () . '/index/cms-user-view-action?user=' . $user . '">' . $info->lastname . ', ' . $info->firstname . '</a>';
}

function msg_date_format ($date) {
	loader_import ('saf.Date');
	return Date::timestamp (
		$date,
		array (
			'today' => '\T\o\d\a\y \a\t g:ia',
			'yesterday' => '\Y\e\s\t\e\r\d\a\y \a\t g:ia',
			'tomorrow' => '\T\o\m\o\r\r\o\w \a\t g:ia',
			'this week' => 'M j, Y',
			'other' => 'M j, Y',
		)
	);
}

echo template_simple ('messages/view.spt', $res);

?>