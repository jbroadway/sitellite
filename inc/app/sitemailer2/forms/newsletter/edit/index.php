<?php

page_title ('SiteMailer 2 - Edit Newsletter');

class Sitemailer2NewsletterEditForm extends MailForm {
	function Sitemailer2NewsletterEditForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/newsletter/edit/settings.php');
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitemailer2-app\'; return false"';

		$list = array ();
		$list = db_pairs ('select id, title from sitemailer2_template order by title asc');
		$list[''] = '- SELECT -';
        
        ksort ($list);
        
		$this->widgets['template']->setValues ($list);

		global $cgi;
		$this->widgets['id']->setValue ($cgi->id);

		$res = db_single ('select * from sitemailer2_newsletter where id = ?', $cgi->id);

		$this->widgets['name']->setValue ($res->name);
		$this->widgets['from_name']->setValue ($res->from_name);
		$this->widgets['from_email']->setValue ($res->from_email);
		$this->widgets['template']->setValue ($res->template);
		$this->widgets['subject']->setValue ($res->subject);
		$this->widgets['public']->setValue ($res->public);
	}

	function onSubmit ($vals) {
		if (! $vals['template']) {
			$vals['template'] = '';
		}
		db_execute (
			'update sitemailer2_newsletter set name = ?, from_name = ?, from_email = ?, template = ?, subject = ?, public = ? where id = ?',
			$vals['name'],
			$vals['from_name'],
			$vals['from_email'],
			$vals['template'],
			$vals['subject'],
			$vals['public'],
			$vals['id']
		);
		header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=saved');
		exit;
	}
}

?>