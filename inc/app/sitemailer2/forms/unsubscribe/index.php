<?php

class Sitemailer2UnsubscribeForm extends MailForm {
	function Sitemailer2UnsubscribeForm () {
		parent::MailForm ();

        page_title ('Unsubscribe');
        
        global $cgi;
        
		$this->parseSettings ('inc/app/sitemailer2/forms/unsubscribe/settings.php');
        
        $this->widgets['newsletter']->setValue ($cgi->nl);
        
	}

	function onSubmit ($vals) {
		// your handler code goes here
        
        return loader_box ('sitemailer2/public/unsubscribe', $vals);
	}
}

?>