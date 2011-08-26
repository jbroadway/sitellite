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

// mailcheck scheduler block
// Checks for new incoming mail.

global $conf;

if (empty ($conf['Messaging']['email_server'])) {
	return;
}

loader_import ('saf.Mail.Pop3');
loader_import ('pear.Mail.mimeDecode');
loader_import ('cms.Workspace.Message');
//loader_import ('cms.Workspace.Task');

$wmsg = new WorkspaceMessage ();
//$wtsk = new WorkspaceTask ();
$pop3 = new Pop3 ($conf['Messaging']['email_server'], $conf['Messaging']['email_port']);

if (! $pop3->connect ()) {
	echo $pop3->error . "\n";
	return;
}

if (! $pop3->authenticate ($conf['Messaging']['email_username'], $conf['Messaging']['email_password'])) {
	echo $pop3->error . "\n";
	return;
}

$messages = $pop3->listMessages ();
foreach ($messages as $number => $message) {
	set_time_limit (30);
	$messages[$number]['message'] = $pop3->getMessage ($number);
	if ($messages[$number]['message'] === false) {
		echo $pop3->error . "\n";
	}

	// parse message and send to system
	$md = new Mail_mimeDecode ($messages[$number]['message']);
	$decoded = $md->decode (array (
		'include_bodies' => true,
		'decode_bodies' => true,
		'decode_headers' => true,
	));

	/* mapping:
	 *
	 * if it's a comment, task, or message		=> from subject \[(C|T|M)([0-9]+)\]
	 *
	 */
	if (preg_match ('/\[(C|T|M)([0-9]+)\] ?/', $decoded->headers['subject'], $regs)) {
		$message_type = $regs[1];
		$message_id = $regs[2];
		$subject = str_replace ($regs[0], '', $decoded->headers['subject']);
	} else {
		// invalid message
		echo 'Invalid subject header: ' . $decoded->headers['subject'] . "\n";
		//return;
	}

	switch ($message_type) {
	/* message
	 *
	 * send(subject,body,recipients,attachments,response_id,priority,from)
	 * subject			=> headers/subject minus []
	 * body				=> body or parts w/ disposition != attachment
	 * recipients		=> empty array
	 * attachments		=> parts w/ disposition == attachment
	 * responding_to	=> from subject
	 * priority			=> headers/x-priority (1 => high)
	 * from				=> headers/from <([^>]+)> use $1
	 *
	 */
		case 'M':

			// put together pieces

			// $subject done
			// $responding_to done ($message_id)
			// $recipients is empty array()

			// priority
			if ($decoded->headers['x-priority'] == 1) {
				$priority = 'high';
			} else {
				$priority = 'normal';
			}

			if (! empty ($decoded->body)) {
				if ($decoded->ctype_secondary == 'plain') {
					$body = $wmsg->formatBody ($decoded->body);
				} else {
					$body = $decoded->body;
				}
				$attachments = array ();
			} else {
				$body = '';
				$attachments = array ();
				foreach ($decoded->parts as $part) {
					if ($part->disposition == 'attachment' || $part->ctype_primary != 'text') {
						$a = array (
							'type' => 'document',
						);
						$a['name'] = $part->d_parameters['filename'];
						$a['body'] = $part->body;
						$a['mime'] = $part->ctype_primary . '/' . $part->ctype_secondary;
						$a['summary'] = '';
						$attachments[] = $a;
					} else {
						if ($part->ctype_secondary == 'plain') {
							$body .= $wmsg->formatBody ($part->body);
						} else {
							$body .= $part->body;
						}
					}
				}
			}

			// - if M{id} is valid
			$res = $wmsg->get ($message_id, false);
			if (! $res) {
				echo 'no such message (' . $message_id . ')' . NEWLINE;
				info ($res, true);
				continue;
			}

			if (preg_match ('/<([^>]+)>/', $decoded->headers['from'], $regs)) {
				$from = $regs[1];
			} else {
				$from = $decoded->headers['from'];
			}

			// - recipient list based on original message
			$list = $wmsg->getRecipients ($message_id);
			if (! is_array ($list)) {
				$list = array ();
			}

			$_list = array ($res->from_user);
			foreach ($list as $obj) {
				if (! $obj->user) {
					$_list[] = $obj->email;
				} else {
					$_list[] = $obj->user;
				}
			}
			$list = $_list;

			$from_user = $wmsg->getUserFromForward ('email', $from);
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

			$id = $wmsg->send ($subject, $body, $list, $attachments, $message_id, $priority, $from_user);
			if (! $id) {
				echo $wmsg->error . "\n";
			} else {
				$pop3->removeMessage ($number);
			}

			break;

	/* task
	 *
	 * comment(taskid,comment,user)
	 * taskid			=> from subject
	 * comment			=> body or parts w/ disposition != attachment
	 *
	 * attach(taskid,type,name,summary,body,mime,user)
	 * taskid			=> from subject
	 * type				=> always 'document'
	 * name				=> from parts/#/ctype_parameters/name
	 * summary			=> from subject
	 * body				=> from parts/#/body
	 * mime				=> from parts/#/ctype_primary . '/' . parts/#/ctype_secondary
	 *
	 *
	 */
		case 'T':

			// put together pieces

			// $taskid done ($message_id)

			if (! empty ($decoded->body)) {
				$body = $decoded->body;
				$attachments = array ();
			} else {
				$body = '';
				$attachments = array ();
				foreach ($decoded->parts as $part) {
					if ($part->disposition == 'attachment' || $part->ctype_primary != 'text') {
						$a = array (
							'type' => 'document',
						);
						$a['name'] = $part->d_parameters['filename'];
						$a['body'] = $part->body;
						$a['mime'] = $part->ctype_primary . '/' . $part->ctype_secondary;
						$attachments[] = $a;
					} else {
						$body .= $part->body;
					}
				}
			}

			if (preg_match ('/<([^>]+)>/', $decoded->headers['from'], $regs)) {
				$from = $regs[1];
			} else {
				$from = $decoded->headers['from'];
			}

			$id = $wtsk->comment ($message_id, $body, $from);
			if (! $id) {
				echo $wtsk->error . "\n";
			} else {
				$erase = true;
				foreach ($attachments as $attachment) {
					$res = $wtsk->attach ($message_id, $attachment['type'], $attachment['name'], '', $attachment['body'], $attachment['mime']);
					if (! $res) {
						echo $wtsk->error . "\n";
						$erase = false;
					}
				}
				if ($erase) {
					$pop3->removeMessage ($number);
				}
			}

			break;

	/* comment
	 *
	 * comment(taskid,comment,user)
	 * taskid			=> from subject
	 * comment			=> body or parts w/ disposition != attachment
	 *
	 * attach(taskid,type,name,summary,body,mime,user)
	 * taskid			=> from subject
	 * type				=> always 'document'
	 * name				=> from parts/#/ctype_parameters/name
	 * summary			=> from subject
	 * body				=> from parts/#/body
	 * mime				=> from parts/#/ctype_primary . '/' . parts/#/ctype_secondary
	 *
	 */
		case 'C':

			// put together pieces

			// $taskid done ($message_id)

			if (! empty ($decoded->body)) {
				$body = $decoded->body;
				$attachments = array ();
			} else {
				$body = '';
				$attachments = array ();
				foreach ($decoded->parts as $part) {
					if ($part->disposition == 'attachment' || $part->ctype_primary != 'text') {
						$a = array (
							'type' => 'document',
						);
						$a['name'] = $part->d_parameters['filename'];
						$a['body'] = $part->body;
						$a['mime'] = $part->ctype_primary . '/' . $part->ctype_secondary;
						$attachments[] = $a;
					} else {
						$body .= $part->body;
					}
				}
			}

			if (preg_match ('/<([^>]+)>/', $decoded->headers['from'], $regs)) {
				$from = $regs[1];
			} else {
				$from = $decoded->headers['from'];
			}

			$id = $wtsk->comment ($message_id, $body, $from);
			if (! $id) {
				echo $wtsk->error . "\n";
			} else {
				$erase = true;
				foreach ($attachments as $attachment) {
					$res = $wtsk->attach ($message_id, $attachment['type'], $attachment['name'], '', $attachment['body'], $attachment['mime']);
					if (! $res) {
						echo $wtsk->error . "\n";
						$erase = false;
					}
				}
				if ($erase) {
					$pop3->removeMessage ($number);
				}
			}

			break;
	} // end switch ($message_type)
}

$pop3->disconnect ();

?>
