<?php

class VerifyvcfForm extends MailForm {
	function VerifyvcfForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/verifyvcf/settings.php');

		$res = db_fetch ('select * from sitemailer2_newsletter');
		if (! $res) {
			$res = array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}

		global $cgi;

       // info ($cgi);
        
       // info ($cgi->group, true);
        $this->widgets['group']->setValue ($cgi->group);
		$this->widgets['firstname']->setValue ($cgi->firstname);
		$this->widgets['lastname']->setValue ($cgi->lastname);
		$this->widgets['organization']->setValue ($cgi->organization);
		if (strstr ($cgi->website, 'http://')) {
			$this->widgets['website']->setValue ($cgi->website);
		} else {
			$this->widgets['website']->setValue ('http://' . $cgi->website);
		}
		$this->widgets['email']->setValue ($cgi->email);
	}

	function onSubmit ($vals) {
        
        db_execute (
			'insert into sitemailer2_recipient
				(id, email, firstname, lastname, organization, website, created)
			values
				(null, ?, ?, ?, ?, ?, now())',
			$vals['email'],
			$vals['firstname'],
			$vals['lastname'],
			$vals['organization'],
			$vals['website']
		);

        $lastid = db_lastid ();
        $vals['group'] = explode (',', $vals['group']);
        
        foreach ($vals['group'] as $group) {
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter
					(recipient, newsletter, status, status_change_time)
				values
					(?, ?, "subscribed", now())',
				$lastid,
				$group
			);
		}

		//header ('Location: ' . site_prefix () . '/index/sitemailer2-subscribers-action?_msg=subcreated');
		//exit;
		page_title ('SiteMailer 2 - Importer');
		echo '<p>Subscriber successfully imported.</p>';
		echo '<p><a href="' . site_prefix () . '/index/sitemailer2-subscribers-action">Return to subscriber list</a></p>';
	}
}

page_title ('SiteMailer 2 - ' . intl_get ('Importer'));
$form = new VerifyvcfForm ();
echo $form->run ();

?>