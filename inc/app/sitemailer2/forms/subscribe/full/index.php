<?php

class Sitemailer2SubscribeFullForm extends MailForm {
	function Sitemailer2SubscribeFullForm () {
		parent::MailForm ();

        global $cgi;
        
        //make sure we need to be here
        $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
        
		$this->parseSettings ('inc/app/sitemailer2/forms/subscribe/full/settings.php');
        
        $this->widgets['email']->setValue($cgi->email);
        
	}

	function onSubmit ($vals) {
		
        return loader_box ('sitemailer2/public/subscribe', $vals);
        
	}
}

?>
