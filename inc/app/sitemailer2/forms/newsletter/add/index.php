<?php

page_title ('SiteMailer 2 - Add Newsletter');

class Sitemailer2NewsletterAddForm extends MailForm {
	function Sitemailer2NewsletterAddForm () {
		parent::MailForm ();
        page_add_script (site_prefix () . '/js/formhelp.js');

		$this->parseSettings ('inc/app/sitemailer2/forms/newsletter/add/settings.php');
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitemailer2-app\'; return false"';

        $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
        
        $this->widgets['from_email']->setValue($settings['email_id']);
        
		$list = array ();
		$list = db_pairs ('select id, title from sitemailer2_template order by title asc');
		$list[''] = '- SELECT -';
        
        ksort ($list);
		$this->widgets['template']->setValues ($list);
	}

	function onSubmit ($vals) {
		if (! $vals['template']) {
			$vals['template'] = '';
		}
		
        
        if(! db_execute (
			'insert into sitemailer2_newsletter (id, name, from_name, from_email, template, subject, public, rss_subs) values (null, ?, ?, ?, ?, ?, ?, 0)',
			$vals['name'],
			$vals['from_name'],
			$vals['from_email'],
			$vals['template'],
			$vals['subject'],
			$vals['public']
        )) { echo "<p>Failed to add newsletter\n</p>";exit; } 

		if (! empty ($vals['newsletters'])) {
			$id = db_lastid ();
			$res = db_pairs ('select distinct recipient, status from sitemailer2_recipient_in_newsletter where newsletter in(' . $vals['newsletters'] . ')');
			foreach ($res as $recip => $status) {
				db_execute (
					'insert into sitemailer2_recipient_in_newsletter (recipient, newsletter, status_change_time, status) values (?, ?, now(), ?)',
					$recip, $id, $status
				);
			}
		}

		header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=created');
		exit;
	}
}

?>