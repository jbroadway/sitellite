<?php

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

// jabbercheck scheduler block
// Checks for new incoming jabber instant messages.

global $conf;

if (empty ($conf['Messaging']['jabber_server'])) {
	return;
}

loader_import ('ext.Jabber');
loader_import ('cms.Workspace.Message');
//loader_import ('cms.Workspace.Task');

$m = new WorkspaceMessage ();
//$wtsk = new WorkspaceTask ();

$j = new Jabber;

$j->resource = 'Sitellite CMS ' . SITELLITE_VERSION;
$j->server = $conf['Messaging']['jabber_server'];
$j->port = $conf['Messaging']['jabber_port'];
$j->username = $conf['Messaging']['jabber_username'];
$j->password = $conf['Messaging']['jabber_password'];
$j->enable_logging = true;

if (! $j->Connect ()) {
	echo $j->log_array[count ($j->log_array) - 1] . NEWLINE;
	return;
}

if (! $j->SendAuth ()) {
	echo $j->log_array[count ($j->log_array) - 1] . NEWLINE;
	return;
}

$j->SendPresence ('available');
sleep (2);
$j->Listen ();

foreach ($j->packet_queue as $message) {

	set_time_limit (30);

	if (! array ($message) || key ($message) != 'message') {
		continue;
	}

	$thread = $j->GetInfoFromMessageThread ($message);
	if (! $thread) {
		continue;
	}

	// now figure out:
	// - type of message (M, T, or C), for now handle only M
	switch (substr ($thread, 0, 1)) {
		case 'M':
			$msg_id = str_replace ('M', '', $thread);
			break;
		case 'T':
		case 'C':
		default:
			continue;
	}

	// - if M{id} is valid
	$res = $m->get ($msg_id, false);
	if (! $res) {
		echo 'no such message (' . $msg_id . ')' . NEWLINE;
		info ($res, true);
		continue;
	}
	
	$from = $j->GetInfoFromMessageFrom ($message);
	$body = $j->GetInfoFromMessageBody ($message);

	$from = preg_replace ('/@([^\/]+)\/.*$/', '@\1', $from);

	// - recipient list based on original message
	$list = $m->getRecipients ($msg_id);

	$_list = array ($res->from_user);
	foreach ($list as $obj) {
		if (! $obj->user) {
			$_list[] = $obj->email;
		} else {
			$_list[] = $obj->user;
		}
	}
	$list = $_list;

	// - internal user based on sitellitem_forward settings
	$from_user = $m->getUserFromForward ('jabber', $from);
	if (! $from_user) {
		continue;
	} elseif (is_object ($from_user)) {
		$from_user = $from_user->user;
		if (! in_array ($from_user, $list)) {
			continue; // can't send if you didn't receive
		}
	} else {
		foreach ($from_user as $u) {
			if (in_array ($u->user, $list)) {
				$from_user = $u->user;
				break;
			}
		}
	}

	// remove self, add $res->from_user
	foreach ($list as $k => $v) {
		if ($v == $from_user) {
			unset ($list[$k]);
		}
	}
	$list = array_unique ($list);

	// - subject based on subject of M{id}
	$subject = Workspace::createResponseSubject ($res->subject);

	// send message
	$res = $m->send (
		$subject,
		$body,
		$list,
		array (),
		$msg_id,
		'normal',
		$from_user
	);

	if (! $res) {
		echo $m->error . NEWLINE;
	}




} // end foreach

$j->enable_logging = false;
$j->Disconnect ();

?>