<?php

$loader->import ('cms.Workspace.Notice');

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceNotice_jabber extends WorkspaceNotice {	
	var $name = 'jabber';

	function send () {
		// send jabber message
		global $loader, $intl, $site, $conf;

		if ($conf['Messaging']['jabber'] == true) {
			$loader->import ('ext.Jabber');

			$jabber = new Jabber;
			$jabber->resource = 'Sitellite CMS ' . SITELLITE_VERSION;
			$jabber->server = $conf['Messaging']['jabber_server'];
			$jabber->port = $conf['Messaging']['jabber_port'];
			$jabber->username = $conf['Messaging']['jabber_username'];
			$jabber->password = $conf['Messaging']['jabber_password'];
			$jabber->enable_logging = true;

			if (! $jabber->Connect ()) {
				$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
				return false;
			}

			if (! $jabber->SendAuth ()) {
				$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
				return false;
			}

			if (strtoupper ($this->type) == 'TASK') {
				$this->id = 'T' . $this->id;
			} elseif (strtoupper ($this->type) == 'MESSAGE') {
				$this->id = 'M' . $this->id;
			} else {
				$this->id = strtoupper (substr ($this->type, 0, 1)) . $this->id;
			}

			if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT')) {
				$subject = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT');
				$subject = $intl->get ($subject, $this);
			} else {
				$subject = '[' . $this->id . '] ' . $this->subject;
			}

			if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY')) {
				$body = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY');
				$body = $intl->get ($body, $this);
			} else {
				$body = $this->body;
			}

			if (! $jabber->SendMessage ($this->address, $this->priority, null,
				array (
					'thread' => $this->id,
					'subject' => $subject, //$intl->get ('New Message Notice!'),
					'body' => $body, //$intl->getf ('You have a new message waiting for you at %s', $site->url),
				)
			)) {
				$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
				return false;
			}
			//echo 'fdsa';

			$jabber->enable_logging = false;
			$jabber->Disconnect ();
			return true;
		} else {
			$this->error = 'Jabber server info not configured';
			return false;
		}
	}

	function sendList ($list) {
		// send jabber message
		global $loader, $intl, $site, $conf;

		if ($conf['Messaging']['jabber'] == true) {
			$loader->import ('ext.Jabber');

			$jabber = new Jabber;
			$jabber->resource = 'Sitellite CMS ' . SITELLITE_VERSION;
			$jabber->server = $conf['Messaging']['jabber_server'];
			$jabber->port = $conf['Messaging']['jabber_port'];
			$jabber->username = $conf['Messaging']['jabber_username'];
			$jabber->password = $conf['Messaging']['jabber_password'];
			$jabber->enable_logging = true;

			if (! $jabber->Connect ()) {
				$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
				return false;
			}

			if (! $jabber->SendAuth ()) {
				$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
				return false;
			}

			foreach ($list as $item) {

				if (strtoupper ($item->type) == 'TASK') {
					$id = 'T' . $item->id;
				} elseif (strtoupper ($item->type) == 'MESSAGE') {
					$id = 'M' . $item->id;
				} else {
					$id = strtoupper (substr ($item->type, 0, 1)) . $item->id;
				}

				if (defined ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_SUBJECT')) {
					$subject = constant ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($item->name) . '_SUBJECT');
					$subject = $intl->get ($subject, $item->struct);
				} else {
					$subject = '[' . $id . '] ' . $item->subject;
				}

				if (defined ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_BODY')) {
					$body = constant ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_BODY');
					$body = $intl->get ($body, $item->struct);
				} else {
					$body = $item->body;
				}

				if (! $jabber->SendMessage ($item->address, $item->priority, null,
					array (
						'thread' => $id,
						'subject' => $subject, //$intl->get ('New Message Notice!'),
						'body' => $body, //$intl->getf ('You have a new message waiting for you at %s', $site->url),
					)
				)) {
					$this->error = $jabber->log_array[count ($jabber->log_array) - 1];
					return false;
				}
				//echo 'fdsa';
			}

			$jabber->enable_logging = false;
			$jabber->Disconnect ();
			return true;
		} else {
			$this->error = 'Jabber server info not configured';
			return false;
		}
	}
}

?>
