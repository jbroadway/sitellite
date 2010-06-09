<?php

class Sitemailer2SubscribeReqForm extends MailForm {
	function Sitemailer2SubscribeReqForm () {
		parent::MailForm ();

        global $cgi;
        
        page_title ('Subscribe');
        
        //make sure we need to be here
        $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
        
		$this->parseSettings ('inc/app/sitemailer2/forms/subscribe/req/settings.php');
        
        $this->widgets['newsletter']->setValue ($cgi->nl);
        
	}

	function onSubmit ($vals) {
		// your handler code goes here
        
        return loader_box ('sitemailer2/public/subscribe', $vals);
        
	}
}

?>
