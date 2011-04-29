<?php

class MassunsubForm extends MailForm {
	function MassunsubForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/massunsub/settings.php');
	}
    
	function onSubmit ($vals) {
        
        if (! empty ($vals['file_csv'])) {
            
            //move the file somewhere temporary that can be controlled
            //move the file to /inc/app/sitemailer2/data/tmp
            //call the filename username
            
            $newfile = '_' . session_username ();
               
            //check if it exists
            if (file_exists (site_docroot  () . '/inc/app/sitemailer2/data/tmp/' . $newfile)) {
                //delete it if it exists
                unlink ('inc/app/sitemailer2/data/tmp/' . $newfile);
            }
            
            //move the file
            rename ($vals['file_csv']->tmp_name, site_docroot  () . '/inc/app/sitemailer2/data/tmp/' . $newfile);
            //header off to verification
            header ('Location: ' . site_prefix () . '/index/sitemailer2-massunsub-action?rc=' . $vals['newsletter']);
            exit;            
        } else {
            echo 'Error importing recipients.';
        }
	}
}

page_title ('SiteMailer 2 - ' . intl_get ('Mass Unsubscriber'));
$form = new MassunsubForm;
echo $form->run (false); // false tells it not to save the uploaded file

?>