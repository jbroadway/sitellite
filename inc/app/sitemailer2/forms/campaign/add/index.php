<?php

class Sitemailer2CampaignAddForm extends MailForm {
	function Sitemailer2CampaignAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitemailer2/forms/campaign/settings.php');
        
        page_title ('Add a Campaign');
        
        $this->widgets['url']->setValue ('http://');
        
        $cancel = site_prefix () . '/index/sitemailer2-campaigns-action';
        
        foreach ($this->widgets['done']->buttons as $k=>$button) {
            if ($button->value == "Cancel") {
                $this->widgets['done']->buttons[$k]->extra = 'onclick="window.location.href=\'' . $cancel . '\'; return false"';
            }
            
        }
	}

	function onSubmit ($vals) {
		// your handler code goes here
        
        if (! db_execute ('insert into sitemailer2_campaign (id, title, forward_url, created) values (NULL, ?, ?, now())', $vals['title'], $vals['url'])) {
            echo 'Failed to create campain';
        } else {
            header ('Location: ' . site_prefix () . '/index/sitemailer2-campaigns-action');
        }
	}
}

?>
