<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// Container class for all functions pertaining to the Sitellite CMS
// Personal Workspace.
//


/**
	 * Container class for all functions pertaining to the Sitellite CMS
	 * Personal Workspace.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $loader->import ('saf.App.Workspace');
	 * 
	 * echo Workspace::createResponseSubject ('Original subject line');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CMS
	 * @category	Workspace
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.net/index/license GNU GPL License
	 * @version	1.0, 2002-10-17, $Id: Workspace.php,v 1.1.1.1 2005/04/29 04:44:31 lux Exp $
	 * @access	public
	 * 
	 */

class Workspace {
	

	/**
	 * Takes a subject line and creates a response subject out
	 * of it.  Responses are prepended with 'Re:' but only the first
	 * (a response of a response of a... doesn't appear as 'Re: Re: Re:'),
	 * and all tags are also stripped from the original subject due
	 * to the necessity of calling htmlentities_reverse() on it, so
	 * that no cross-site scripting attacks can be achieved indirectly
	 * through subject reiteration.
	 * 
	 * @access	public
	 * @param	string	$sub
	 * @return	string
	 * 
	 */
	function createResponseSubject ($sub) {
		if (preg_match ('/^Re: /', $sub)) {
			return $sub;
		} else {
			return 'Re: ' . strip_tags (htmlentities_reverse ($sub));
		}
	}

	/**
	 * Takes a username and the target of the message or task and
	 * creates a link to that user's profile if it is an individual user
	 * or just returns the name and target as a string otherwise.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$target
	 * @return	string
	 * 
	 */
	function createUserLink ($name, $target) {
		if ($target == 'user') {
			return '<a href="viewUser.php?name=' . $name . '">' . $name . '</a> ' . $target;
		} else {
			return $name . ' ' . $target;
		}
	}

	/**
	 * Takes an array of attachment objects (records from the
	 * sitellite_attachment table) and displays them one-per-line as
	 * a string.
	 * 
	 * @access	public
	 * @param	array	$attachments
	 * @return	string
	 * 
	 */
	function formatAttachments ($attachments = array ()) {
		global $tables, $intl;
		$out = '';
		foreach ($attachments as $a) {
			if ($a->type == 'database') {
				$out .= $intl->get ('Database') . ': <a href="find.php?table=' . $a->tableorpath . '&column=' . $tables[$a->tableorpath]->primary_key . '&query=' . $a->idvalue . '">' . $a->tableorpath . '.' . $a->idvalue . "</a><br />\n";
			} elseif ($a->type == 'filesystem') {
				$out .= $intl->get ('Filesystem') . ': <a href="ffind.php?path=' . urlencode (dirname ($a->idvalue)) . '&pattern=' . urlencode (basename ($a->idvalue)) . '&usepath=cwd">' . $a->idvalue . "</a><br />\n";
			} elseif ($a->type == 'page') {
				$out .= $intl->get ('Link') . ': <a href="' . $a->idvalue . '" target="_blank">' . $a->idvalue . "</a><br />\n";
			} elseif ($a->type == 'document') {
				$out .= $intl->get ('Document') . ': <a href="getDocumentAttachment.php?id=' . $a->id . '&msgid=' . $a->linktouid . '&from=' . $a->linktotable . '">' . $a->idvalue . "</a><br />\n";
			} elseif ($a->type == 'search') {
				list ($col, $query) = preg_split ('/=/', $a->idvalue);
				$out .= $intl->get ('Search') . ': <a href="find.php?table=' . $a->tableorpath . '&column=' . $col . '&query=' . $query . '">' . $a->tableorpath . '.' . $a->idvalue . "</a><br />\n";
			}
		}
		return $out;
	}

	/**
	 * Creates an "in response to" HTML table row for display
	 * in messages that are responses to another message, specified by
	 * the $id parameter.  $current is the id of the current message.
	 * 
	 * @access	public
	 * @param	integer	$id
	 * @param	integer	$current
	 * @return	string
	 * 
	 */
	function response ($id, $current) {
		if ($id > 0) {
			global $intl, $db;
			$out = '<tr><td class="tinted">' . $intl->get ('In Response To') . '</td><td class="odd">';
			$row = $db->fetch ('select subject from sitellite_message where id = ?', $id);
			if (is_object ($row)) {
				$out .= '<a href="readMessage.php?id=' . $id . '">' . $row->subject . '</a>';
			} else {
				$out .= '<a href="readMessage.php?id=' . $id . '">' . $intl->get ('Message Number') . ' ' . $id . '</a>';
			}
			$out .= ' &nbsp; [ <a href="readMessage.php?view=thread&id=' . $id . '">' . $intl->get ('Read Thread') . '</a> ]';
			return $out . '</td></tr>';
		} elseif ($responses = Workspace::isThread ($current, $id)) {
			global $intl;
			return '<tr><td class="tinted">' . $intl->get ('Responses') . '</td><td class="odd">' . $responses . ' &nbsp; [ <a href="readMessage.php?view=thread&id=' . $current . '">' . $intl->get ('Read Thread') . '</a> ]</td></tr>';
		} else {
			return '';
		}
	}

	/**
	 * Returns the number of responses to a specific message (not
	 * recursively), specified by the $id parameter.
	 * 
	 * @access	public
	 * @param	integer	$id
	 * @return	integer
	 * 
	 */
	function countResponses ($id) {
		global $db;
		$count = 0;
		$res = $db->fetch ('select id from sitellite_message where responding_to = ?', $id);
		if (! $res) {
			return $count;
		} elseif (is_object ($res)) {
			return 1 ;// + count_responses ($res->id);
		}
		foreach ($res as $row) {
			$count += 1 ;// + count_responses ($row->id);
		}
		return $count;
	}

	/**
	 * Creates a 'Thread' link if $responses or $responding_to
	 * are greater than 0 (one of, not necessarily both).
	 * 
	 * @access	public
	 * @param	integer	$id
	 * @param	integer	$responses
	 * @param	integer	$responding_to
	 * @return	string
	 * 
	 */
	function displayThreadLink ($id, $responses, $responding_to) {
		global $intl;
		if ($responses > 0 || $responding_to > 0) {
			return '[ <a href="readMessage.php?view=thread&id=' . $id . '">' . $intl->get ('Thread') . '</a> ]';
		}
		return '';
	}

	/**
	 * Determines whether the specified message or task belongs
	 * to the current user (belonging as in "is targeted for them",
	 * not necessarily that they originated the item).  $table can
	 * be either 'messages' or 'tasks'.  $id is the unique id of the
	 * message.  The user's information is taken from the global
	 * $session object.
	 * 
	 * @access	public
	 * @param	string	$table
	 * @param	integer	$id
	 * @return	boolean
	 * 
	 */
	function belongsToUser ($table = 'messages', $id) {
		global $db, $session;

		if ($table == 'messages') {
			$res = $db->fetch ('select count(*) as total from sitellite_message where id = ? and
				(
					(receiver = ? and target = "user") or
					(receiver = ? and target = "team") or
					(receiver = ? and target = "role") or
					(target = "all")
				)',
				$id,
				$session->username,
				$session->team,
				$session->role
			);
			if (! $res) {
				return false;
			} else {
				return $res->total;
			}
		} elseif ($table == 'tasks') {
			$res = $db->fetch ('select count(*) as total from sitellite_task where id = ? and
				(
					(owner = ? and target = "user") or
					(owner = ? and target = "team") or
					(owner = ? and target = "role") or
					(target = "all")
				)',
				$id,
				$session->username,
				$session->team,
				$session->role
			);
			if (! $res) {
				return false;
			} else {
				return $res->total;
			}
		}
	}

	/**
	 * Determines whether the specified message was sent by
	 * the current user.  $id is the unique id of the message.  The
	 * user's information is taken from the global $session object.
	 * 
	 * @access	public
	 * @param	integer	$id
	 * @return	boolean
	 * 
	 */
	function sentByUser ($id) {
		global $db, $session;

		$res = $db->fetch ('select count(*) as total from sitellite_message where id = ? and sender = ?',
			$id,
			$session->username
		);
		if (! $res) {
			return false;
		} else {
			return $res->total;
		}
	}

	/**
	 * Determines whether the specified message is a part
	 * of a thread or not, based on its $id and $responding_to
	 * values.
	 * 
	 * @access	public
	 * @param	integer	$id
	 * @param	integer	$responding_to
	 * @return	boolean
	 * 
	 */
	function isThread ($id, $responding_to) {
		if ($responding_to) {
			return true;
		} else {
			global $db;
			$res = $db->fetch ('select count(*) as total from sitellite_message where responding_to = ?' , $id);
			if (! is_object ($res)) {
				return false;
			} else {
				return $res->total;
			}
		}
	}

	/**
	 * Takes a user's session identifier and session expiry
	 * timestamp and checks to see if they represent a currently
	 * active session.  Note: This method does not verify that
	 * $session_id is a valid session identifier, but rather
	 * expects these values to have come from a database source
	 * and hence be validated prior to this method being called.
	 * If the $session_id value is empty however, it will return
	 * false.  Compares $expires against the current time to
	 * determine if the session has expired or not.
	 * 
	 * @access	public
	 * @param	string	$session_id
	 * @param	integer	$expires
	 * @return	boolean
	 * 
	 */
	function isActive ($session_id, $expires) {
		if (empty ($session_id)) {
			return false;
		}
		global $loader;
		$loader->import ('saf.Date');
		if (Date::compare ($expires, Date::toUnix ()) >= 0) {
			return false;
		}
		return true;
	}
	
}



?>