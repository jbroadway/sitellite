<?php

class Sitemailer2CampaignEditForm extends MailForm {
	function Sitemailer2CampaignEditForm () {
		parent::MailForm ();

        global $cgi;
        
		$this->parseSettings ('inc/app/sitemailer2/forms/campaign/settings.php');
        
        page_title ('Edit a Campaign');
        
        $this->widgets['id']->setValue ($cgi->id);
        
        $res = db_single ('select * from sitemailer2_campaign where id = ?', $cgi->id);
        
        //info ($res);
        
        $this->widgets['title']->setValue ($res->title);
        $this->widgets['url']->setValue ($res->forward_url);
        
	}

	function onSubmit ($vals) {
		// your handler code goes here
        
        if (! db_execute ('update sitemailer2_campaign set title = ?, forward_url = ? where id = ?', $vals['title'], $vals['url'], $vals['id'])) {
            echo 'Failed to update campain';
        } else {
            header ('Location: ' . site_prefix () . '/index/sitemailer2-campaigns-action');
        }
	}
}

?>
