<?php

$loader->import ('cms.Workspace.Notice');

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceNotice_email extends WorkspaceNotice {	
	var $name = 'email';

	function send () {
		// send email of message
		global $loader, $intl, $conf;
		$loader->import ('saf.Ext.phpmailer');

		$mail = new phpmailer ();
		$mail->IsMail ();
		$mail->IsHTML (true);

		if (strtoupper ($this->type) == 'TASK') {
			$this->id = 'T' . $this->id;
		} elseif (strtoupper ($this->type) == 'MESSAGE') {
			$this->id = 'M' . $this->id;
		} else {
			$this->id = strtoupper (substr ($this->type, 0, 1)) . $this->id;
		}

		$mail->From = $conf['Messaging']['return_address'];
		//$mail->Subject = '[' . $this->id . '] ' . $this->subject;
		//$mail->Body = $this->body;
		$mail->AddAddress ($this->address);

		if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT')) {
			$subject = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT');
			$mail->Subject = $intl->get ($subject, $this);
		} else {
			$mail->Subject = '[' . $this->id . '] ' . $this->subject;
		}

		if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY')) {
			$body = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY');
			$mail->Body = $intl->get ($body, $this);
		} else {
			$mail->Body = $this->body;
		}

		if ($this->priority == 'urgent' || $this->priority == 'high') {
			$mail->Priority = 1;
		}

		if ($mail->Send ()) {
			return true;
		}

		$this->error = $mail->ErrorInfo;
		return false;
	}

	function sendList ($list) {
		// send email of message
		global $loader, $intl, $conf;
		$loader->import ('saf.Ext.phpmailer');

		$mail = new phpmailer ();
		$mail->IsMail ();
		$mail->IsHTML (true);

		foreach ($list as $item) {
			if (strtoupper ($item->type) == 'TASK') {
				$id = 'T' . $item->id;
			} elseif (strtoupper ($item->type) == 'MESSAGE') {
				$id = 'M' . $item->id;
			} else {
				$id = strtoupper (substr ($item->type, 0, 1)) . $item->id;
			}

			$mail->From = $conf['Messaging']['return_address'];
			//$mail->Subject = '[' . $this->id . '] ' . $this->subject;
			//$mail->Body = $this->body;
			$mail->AddAddress ($item->address);

			if (defined ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_SUBJECT')) {
				$subject = constant ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_SUBJECT');
				$mail->Subject = $intl->get ($subject, $item->struct);
			} else {
				$mail->Subject = '[' . $id . '] ' . $item->subject;
			}

			if (defined ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_BODY')) {
				$body = constant ('WORKSPACE_' . strtoupper ($item->type) . '_' . strtoupper ($this->name) . '_BODY');
				$mail->Body = $intl->get ($body, $item->struct);
			} else {
				$mail->Body = $item->body;
			}

			if ($item->priority == 'urgent' || $item->priority == 'high') {
				$mail->Priority = 1;
			} else {
				$mail->Priority = 3;
			}

			if (! $mail->Send ()) {
				$this->error = $mail->ErrorInfo;
				return false;
			}

			$mail->ClearAddresses ();
			$mail->ClearAttachments ();
		}

		return true;
	}
}

?>
