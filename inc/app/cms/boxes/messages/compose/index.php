<?php

loader_import ('saf.MailForm');

class ComposeForm extends MailForm {
	function ComposeForm () {
		parent::MailForm ();

		$t =& $this->addWidget ('hidden', 'response_id');

		$t =& $this->addWidget ('text', 'subject');
		$t->alt = intl_get ('Subject');
		$t->addRule ('not empty', intl_get ('You must enter a subject line.'));
		$t->attr ('size', 50);

		$t =& $this->addWidget ('cms.Widget.Recipients', 'recipients');
		$t->alt = intl_get ('Send To');
		$t->addRule ('not empty', intl_get ('You must enter at least one recipient.'));

		$t =& $this->addWidget ('select', 'priority');
		$t->setValues (array (
			'normal' => intl_get ('Normal'),
			'high' => intl_get ('High'),
			'urgent' => intl_get ('Urgent'),
		));
		$t->alt = intl_get ('Priority');

		$t =& $this->addWidget ('xed.Widget.Xeditor', 'body');
		$this->extra = 'onsubmit="xed_copy_value (this, \'body\')"';
		$t->addRule ('not empty', intl_get ('You must enter a message to be sent.'));

		$t =& $this->addWidget ('msubmit', 'submit_button');
		$b =& $t->getButton ();
		$b->setValues (intl_get ('Send'));
		$b->extra = 'onclick="onbeforeunload_form_submitted=true; cms_recipient_select_all (this.form)"';
		$b =& $t->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="onbeforeunload_form_submitted=true; window.location.href = \'' . site_prefix () . '/index/cms-cpanel-action\'; return false"';

		$this->error_mode = 'all';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Workspace.Message');

		$msg = new WorkspaceMessage;

		if (! $vals['response_id']) {
			$vals['response_id'] = '0';
		}

		$res = $msg->send (
			$vals['subject'],
			$vals['body'],
			explode (',', $vals['recipients']),
			array (),
			$vals['response_id'],
			$vals['priority'],
			session_username ()
		);

		if (! $res) {
			echo '<p>Error: ' . $msg->error . '</p>';
		}

		session_set ('sitellite_alert', intl_get ('Your message has been sent.'));

		header ('Location: ' . site_prefix () . '/index/cms-cpanel-action?_msg=sent');
		exit;
	}
}

page_title (intl_get ('Composing Message'));
$form = new ComposeForm ();
echo $form->run ();

?>
