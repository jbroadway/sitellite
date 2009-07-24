<?php

$loader->import ('cms.Workspace.Notice');

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceNotice_sms extends WorkspaceNotice {
	var $charlimit = 130;	
	var $name = 'sms';

	function send () {
		// send email of message
		global $loader, $intl, $conf;
		$loader->import ('saf.Ext.phpmailer');

		$mail = new phpmailer ();
		$mail->IsMail ();

		if (strtoupper ($this->type) == 'TASK') {
			$this->id = 'T' . $this->id;
		} elseif (strtoupper ($this->type) == 'MESSAGE') {
			$this->id = 'M' . $this->id;
		} else {
			$this->id = strtoupper (substr ($this->type, 0, 1)) . $this->id;
		}

		$mail->From = $conf['Messaging']['return_address'];

		if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT')) {
			$subject = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_SUBJECT');
			$mail->Subject = $intl->get ($subject, $this);
		} else {
			$mail->Subject = '[' . $this->id . '] ' . $intl->get ('Notice');
		}

		if (defined ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY')) {
			$body = constant ('WORKSPACE_' . strtoupper ($this->type) . '_' . strtoupper ($this->name) . '_BODY');
			$mail->Body = $intl->get ($body, $this);
		} else {
			$mail->Body = $this->subject;
		}

		// message body should be less than $this->charlimit characters
		$mail->Body = substr ($mail->Body, 0, $this->charlimit);

		$mail->AddAddress ($this->address);

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
		$this->error = 'Not implemented yet!';
		return false;
	}
}

?>
