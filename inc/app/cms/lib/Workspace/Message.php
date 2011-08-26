<?php

$loader->import ('cms.Workspace');
$loader->import ('cms.Workspace.Message.Category');

define ('WORKSPACE_MESSAGE_SMS_SUBJECT', '[{id}] New Message Notice');
define ('WORKSPACE_MESSAGE_SMS_BODY', '<?php
	$obj->subject = substr ($obj->subject, 0, 32);
' . CLOSE_TAG . 'New message {id} ({subject}...) at {site/domain}');

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceMessage {
	var $error;
	var $category;

	function WorkspaceMessage () {
		$this->category = new WorkspaceMessageCategory;
	}

	function getRecipients ($id) {
		// get recipient list as user/type/email (email only if type=user)
		$res = db_fetch (
			'select user, type from sitellite_msg_recipient where message_id = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}

		$recv = $res;

		foreach ($recv as $key => $value) {
			if ($value->type == 'user') {
				$recv[$key]->email = db_shift (
					'select email from sitellite_user where username = ?',
					$value->user
				);
			}
		}

		$res = db_fetch (
			'select from_user from sitellite_message where id = ?',
			$id
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		if (preg_match ('/^email:/', $res->from_user)) {
			$sender = new StdClass;
			$sender->user = str_replace ('email:', '', $res->from_user);
			$sender->type = 'email';
		} else {
			$sender = new StdClass;
			$sender->user = $res->from_user;
			$sender->type = 'user';
		}

		array_unshift ($recv, $sender);

		return $recv;
	}

	function getUserFromForward ($location, $info) {
		return db_fetch (
			'select user from sitellite_msg_forward where location = ? and info = ?', $location, $info
		);
	}

	function send ($subject, $body, $recipients, $attachments = array (), $response_id = 0, $priority = 'normal', $from = '') {
		global $db, $session, $loader, $intl, $site;
		$db->execute ('begin');

		if (empty ($from)) {
			$from = $session->username;
		}

		// if $responding_to, get $recipients list from previous message's sender and recipient list
		// also verify that the sender is in the recipient list (and remove him this time)
		/*if ($response_id != 0) {
			$receivers = $this->getRecipients ($response_id);

			if (! is_array ($receivers)) {
				return false;
			}
			$sender = array_shift ($receivers);

			// make sure current sender is in $receivers
			$in = false;
			foreach ($receivers as $key => $recv) {
				if ($from == $recv->user) {
					if ($recv->type == 'email') { // if recipient was a cc'd email address, mark that in $from
						$from = 'email:' . $recv->user;
					}
					unset ($receivers[$key]);
					$in = true;
					break;
				} elseif ($recv->type == 'user' && $from == $recv->email) { // check if email matches address of user-type recipient
					$from = $recv->user;
					unset ($receivers[$key]);
					$in = true;
					break;
				}
			}

			if (! $in) {
				$this->error = 'Sender is not in original recipient list';
				$db->execute ('rollback');
				return false;
			}

			$receivers[] = $sender;
			$recipients = array_merge ($receivers, $recipients); // add new $recipients to receiver list
		}*/

		$res = $db->execute (
			'insert into sitellite_message
				(id, subject, msg_date, from_user, priority, response_id, body)
			values
				(null, ?, now(), ?, ?, ?, ?)',
			$subject, $from, $priority, $response_id, $body
		);
		if (! $res) {
			$this->error = $db->error;
			$db->execute ('rollback');
			return false;
		}

		$id = $db->lastid;/*

		if (! is_array ($attachments)) {
			$attachments = array ();
		}*/

		// attachments is 2D array with inner elements: type, name, summary, body, mime
		foreach ($attachments as $attachment) {
			$res = $db->execute (
				'insert into sitellite_msg_attachment
					(id, type, name, message_id, summary, body, mimetype)
				values
					(null, ?, ?, ?, ?, ?, ?)',
				$attachment['type'], $attachment['name'], $id, $attachment['summary'], $attachment['body'], $attachment['mime']
			);
			if (! $res) {
				$this->error = $db->error;
				$db->execute ('rollback');
				return false;
			}
		}

		// recipients is 2D array with inner elements: type, user
		if (! is_array ($recipients)) {
			$recipients = array ($recipients);
		}
		foreach ($recipients as $recipient) {
			if (is_object ($recipient)) {
				$recipient = get_object_vars ($recipient);
			} elseif (is_string ($recipient)) {
				$recipient = array (
					'type' => 'user',
					'user' => $recipient,
				);
			}
			if (! isset ($recipient['type'])) {
				$recipient['type'] = 'user';
			}

			$res = $db->execute (
				'insert into sitellite_msg_recipient
					(id, type, user, message_id, category, status)
				values
					(null, ?, ?, ?, "", "unread")',
				$recipient['type'], $recipient['user'], $id
			);
			if (! $res) {
				$this->error = $db->error;
				$db->execute ('rollback');
				return false;
			}

			// check for forwards
			if ($recipient['type'] == 'user') {
				/*
				$res = $db->fetch (
					'select * from sitellite_msg_forward where
						user = ? and
						(priority = ? or priority = "all")',
					$recipient['user'], $priority
				);
				if (is_array ($res)) {
					$res = $res[0];
				}
				*/
				$res = (object) array (
					'info' => db_shift ('select email from sitellite_user where username = ?', $recipient['user']),
					'location' => 'email',
				);
				if (is_object ($res)) {
					// send forward
					$loader->import ('cms.Workspace.Notice.' . ucfirst ($res->location));
					$obj = 'WorkspaceNotice_' . ucfirst ($res->location);
					$forward = new $obj (
						'message',
						$res->info,
						$id,
						$subject,
						$body,
						$priority
					);

					if (! $forward->send ()) {
						echo $res->location . ': ' . $forward->error . "\n";			// <-- !!!!!
					}

				}
			} elseif ($recipient['type'] == 'email') {
				$loader->import ('cms.Workspace.Notice.Email');
				$forward = new WorkspaceNotice_email ('message', $recipient['user'], $id, $subject, $body, $priority);
				$forward->send ();
			}
		}

		$db->execute ('commit');
		return $id;
	} // end send()

	function exists ($id) {
		return db_execute ('select count(*) from sitellite_message where id = ?', $id);
	}

	function get ($id, $user = true) {
		if ($user) {
			$sql = 'select
				*
			from
				sitellite_message m, sitellite_msg_recipient r
			where
				r.message_id = m.id and
				r.user = ? and
				m.id = ?';
			$bind = array (session_username (), $id);
		} else {
			$sql = 'select
				*
			from
				sitellite_message
			where
				id = ?';
			$bind = array ($id);
		}

		$res = db_fetch ($sql, $bind);

		if (! $res) {
			$this->error = db_error ();
			return $res;
		} elseif (is_array ($res)) {
			$res = $res[0];
		}

		// now we need the attachments
		$a = db_fetch ('
			select * from sitellite_msg_attachment where message_id = ?',
			$id
		);
		if (! $a) {
			$res->attachments = array ();
		} elseif (is_object ($a)) {
			$res->attachments = array ($a);
		} else {
			$res->attachments = array ();
			foreach ($a as $attachment) {
				$res->attachments[] = $a;
			}
		}

		if (! $user) {
			return $res;
		}

		// mark message 'read' in sitellite_recipient status
		db_execute ('
			update sitellite_msg_recipient
			set status = "read"
			where message_id = ? and
			status = "unread" and
			type = "user" and
			user = ?',
			$id,
			session_username ()
		);

		return $res;
	} // end get()

	// returns id, subject, msg_date, from_user, priority, and status
	function getNew () {
		global $db, $session;
		$res = $db->fetch ('
			select
				m.id, m.subject, m.msg_date, m.from_user, m.priority, r.status
			from
				sitellite_msg_recipient r, sitellite_message m
			where
				r.message_id = m.id and
				r.user = ? and
				r.status = "unread"
			order by
				m.msg_date desc',
			$session->username
		);

		if (! $res) {
			$this->error = $db->error;
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}

		return $res;
	} // end getNew ()

	// returns id, subject, msg_date, from_user, priority, and status
	function getFolder ($category = '', $search = false, $limit = 10, $offset = 0, $orderBy = 'msg_date', $sort = 'desc') {
		if ($search) {
			$q = db_query ('
				select
					m.id, m.subject, m.msg_date, m.from_user, m.priority, r.status
				from
					sitellite_msg_recipient r, sitellite_message m
				where
					r.message_id = m.id and
					r.user = ? and
					r.status != "trash" and
					match(m.subject,m.body) against(?)'
			);
		} else {
			$q = db_query ('
				select
					m.id, m.subject, m.msg_date, m.from_user, m.priority, r.status
				from
					sitellite_msg_recipient r, sitellite_message m
				where
					r.message_id = m.id and
					r.user = ? and
					r.status != "trash" and
					r.category = ?
				order by
					' . $orderBy . ' ' . $sort
			);
		}

		if ($q->execute (session_username (), $category)) {
			$this->total = $q->rows ();
			$res = $q->fetch ($offset, $limit);
			$q->free ();
			return $res;
		} else {
			$this->error = $q->error ();
			return false;
		}
	} // end getMessages()

	// returns id, subject, msg_date, from_user, priority, and status
	function getTrash ($limit = 10, $offset = 0, $orderBy = 'msg_date', $sort = 'desc') {
		$q = db_query ('
			select
				m.id, m.subject, m.msg_date, m.from_user, m.priority, r.status
			from
				sitellite_msg_recipient r, sitellite_message m
			where
				r.message_id = m.id and
				r.user = ? and
				r.status = "trash"
			order by
				' . $orderBy . ' ' . $sort
		);

		if ($q->execute (session_username ())) {
			$this->total = $q->rows ();
			$res = $q->fetch ($offset, $limit);
			$q->free ();
			return $res;
		} else {
			$this->error = $q->error ();
			return false;
		}
	} // end getMessages()

	// returns id, subject, msg_date, priority
	function getSent ($id = false, $search = false, $limit = 10, $offset = 0, $orderBy = 'msg_date', $sort = 'desc') {
		if ($id === false || $search !== false) { // get all

			if ($search) {
				$q = db_query ('
					select
						id, subject, msg_date, priority
					from
						sitellite_message
					where
						from_user = ? and
						match(subject,body) against(?)'
				);
				$bind = array (session_username (), $id);
			} else {
				$q = db_query ('
					select
						id, subject, msg_date, priority
					from
						sitellite_message
					where
						from_user = ?
					order by
						' . $orderBy . ' ' . $sort
				);
				$bind = array (session_username ());
			}

			if ($q->execute ($bind)) {
				$this->total = $q->rows ();
				$res = $q->fetch ($offset, $limit);
				$q->free ();
			} else {
				$this->error = $q->error ();
				return false;
			}

			global $db, $session;

			// get recipients
			foreach ($res as $key => $row) {
				$r = $db->fetch ('
					select * from sitellite_msg_recipient where message_id = ?',
					$row->id
				);
				if (! $r) {
					$res[$key]->recipients = array ();
				} elseif (is_object ($r)) {
					$res[$key]->recipients = array ($r);
				} else {
					$res[$key]->recipients = $r;
				}
			}

			return $res;

		} else { // get single

			global $db, $session;

			$res = $db->fetch ('
				select
					*
				from
					sitellite_message
				where
					from_user = ? and
					id = ?',
				$session->username,
				$id
			);

			if (! $res) {
				$this->error = $db->error;
				return $res;
			} elseif (is_array ($res)) {
				$res = $res[0];
			}

			// now we need the recipients
			$r = $db->fetch ('
				select * from sitellite_msg_recipient where message_id = ?',
				$id
			);
			if (! $r) {
				$res->recipients = array ();
			} elseif (is_object ($r)) {
				$res->recipients = array ($r);
			} else {
				$res->recipients = array ();
				foreach ($r as $recipient) {
					$res->recipients[] = $r;
				}
			}

			// now we need the attachments
			$a = $db->fetch ('
				select * from sitellite_msg_attachment where message_id = ?',
				$id
			);
			if (! $a) {
				$res->attachments = array ();
			} elseif (is_object ($a)) {
				$res->attachments = array ($a);
			} else {
				$res->attachments = array ();
				foreach ($a as $attachment) {
					$res->attachments[] = $a;
				}
			}

			return $res;

		}
	} // end getMessages()

	function getXML ($id) {
		$res = $this->get ($id);
		if (! $res) {
			return false;
		}
		$out = '<message id="' . $id . "\">\n";
		foreach (get_object_vars ($res) as $key => $value) {
			if ($key == 'id' || $key == 'message_id' || is_array ($value)) {
				continue;
			}
			$out .= "\t<" . $key . '>' . htmlentities ($value) . '</' . $key . ">\n";
		}

		$out .= "\t<attachments>\n";
		foreach ($res->attachments as $key => $obj) {
			$out .= "\t\t<attachment id=\"" . $obj->id . ">\n";
			foreach (get_object_vars ($obj) as $k => $value) {
				if ($k == 'id') {
					continue;
				}
				$out .= "\t\t\t<" . $k . '>' . htmlentities ($value) . '</' . $k . ">\n";
			}
			$out .= "\t\t</attachment>\n";
		}
		$out .= "\t</attachments>\n";

		$out .= '</message>';
		return $out;
	}

	function getSentXML ($id) {
		$res = $this->getSent ($id);
		if (! $res) {
			return false;
		}
		$out = '<message id="' . $id . "\">\n";
		foreach (get_object_vars ($res) as $key => $value) {
			if ($key == 'id' || $key == 'message_id' || is_array ($value)) {
				continue;
			}
			$out .= "\t<" . $key . '>' . htmlentities ($value) . '</' . $key . ">\n";
		}

		$out .= "\t<recipients>\n";
		foreach ($res->recipients as $key => $obj) {
			$out .= "\t\t<recipient id=\"" . $obj->id . ">\n";
			foreach (get_object_vars ($obj) as $k => $value) {
				if ($k == 'id' || $k == 'message_id') {
					continue;
				}
				$out .= "\t\t\t<" . $k . '>' . htmlentities ($value) . '</' . $k . ">\n";
			}
			$out .= "\t\t</recipient>\n";
		}
		$out .= "\t</recipients>\n";

		$out .= "\t<attachments>\n";
		foreach ($res->attachments as $key => $obj) {
			$out .= "\t\t<attachment id=\"" . $obj->id . ">\n";
			foreach (get_object_vars ($obj) as $k => $value) {
				if ($k == 'id' || $k == 'message_id') {
					continue;
				}
				$out .= "\t\t\t<" . $k . '>' . htmlentities ($value) . '</' . $k . ">\n";
			}
			$out .= "\t\t</attachment>\n";
		}
		$out .= "\t</attachments>\n";

		$out .= '</message>';
		return $out;
	}

	function search ($query) {
		return $this->getFolder ($query, true);
	}

	function searchSent ($query) {
		return $this->getSent ($query, true);
	}

	function setCategory ($id, $category) {
		$res = db_execute ('
			update sitellite_msg_recipient
			set category = ?
			where message_id = ? and
			type = ? and
			user = ?',
			$category,
			$id,
			'user',
			session_username ()
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function trash ($id) {
		$res = db_execute ('
			update sitellite_msg_recipient
			set status = "trash"
			where message_id = ? and
			type = "user" and
			user = ?',
			$id,
			session_username ()
		);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function addCategory ($name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}

		$res = $this->category->add ($name, $user);
		if (! $res) {
			$this->error = $this->category->error;
		}
		return $res;
	}

	function renameCategory ($new, $name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}

		$res = $this->category->rename ($new, $name, $user);
		if (! $res) {
			$this->error = $this->category->error;
			return false;
		}

		// update messages with new category now
		$res = db_execute ('
			update sitellite_msg_recipient
			set category = ?
			where category = ? and
			type = ? and
			user = ?',
			$new,
			$name,
			'user',
			$user
		);
		if (! $res) {
			$this->error = db_error ();
		}

		return $res;
	}

	function deleteCategory ($name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}

		// check for messages with category now
		$count = db_shift ('
			select count(*) from sitellite_msg_recipient
			where type = ? and user = ? and category = ?',
			'email',
			$user,
			$name
		);
		if ($count > 0) {
			$this->error = 'Folder must be empty to delete it.';
			return false;
		}

		$res = $this->category->delete ($name, $user);
		if (! $res) {
			$this->error = $this->category->error;
		}
		return $res;
	}

	function categories ($user = false) {
		if (! $user) {
			$user = session_username ();
		}

		return $this->category->getList ($user);
	}

	function formatBody ($txt) {
		$html = preg_replace ('/(\r\n|\n\r|\r|\n)/s', '<br />\1', htmlentities_compat ($txt));
		$html = preg_replace ('/(\r\n|\n\r|\r|\n)&gt;([^\r\n]*)/s', '\1<blockquote>\2</blockquote>', $html);
		$html = preg_replace ('/<\/blockquote>([\r\n]+)<blockquote>/s', '\1', $html);
		return $html;
	}
}

?>
