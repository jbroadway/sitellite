<?php

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceNotice {
	var $address;
	var $id;
	var $subject;
	var $body;
	var $priority;
	var $type;
	var $error;
	var $types = array ('sms' => 'SMS', 'jabber' => 'Jabber', 'email' => 'Email');

	function WorkspaceNotice ($type = '', $address = '', $id = '', $subject = '', $body = '', $priority = 'normal') {
		$this->type = $type;
		$this->address = $address;
		$this->id = $id;
		$this->subject = $subject;
		$this->body = $body;
		$this->priority = $priority;
	}

	function queue () {
		$res = db_execute ('insert into sitellite_msg_queue (id, type, struct) values (null, ?, ?)', $this->name, serialize ($this));
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function getQueue ($type = false) {
		if (! $type) {
			$type = $this->name;
		}

		loader_import ('cms.Workspace.Notice.' . $this->types[$type]);

		$res = db_fetch ('select * from sitellite_msg_queue where type = ?', $type);
		if (! $res) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}

		foreach ($res as $k => $v) {
			$qid = $res[$k]->id;
			$struct = $res[$k]->struct;
			$res[$k] = unserialize ($struct);
			if (! $res[$k]) {
				$this->error = 'unserialize() failed: ' . $struct;
				return false;
			}
			$res[$k]->qid = $qid;
		}

		return $res;
	}

	function delete ($id) {
		$res = db_execute ('delete from sitellite_msg_queue where id = ?', $id);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function flushQueue ($type) {
		$res = $this->getQueue ($type);
		if (! $res) {
			return false;
		}

		if (! $res[0]->sendList ($res)) {
			$this->error = $res[0]->error;
			return false;
		}

		foreach ($res as $row) {
			$this->delete ($row->qid);
		}

		return count ($res);
	}

	function send () {
		return true;
	}

	function sendList ($list) {
		return true;
	}
}

?>