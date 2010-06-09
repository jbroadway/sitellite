<?php

global $cgi;


class Sitemailer2TemplateForm extends MailForm {
	function Sitemailer2TemplateForm () {
		parent::MailForm ();

		global $cgi;
		loader_import ('cms.Versioning.Rex');
		$rex;
	
		
		//if add is true, we're creating a template/msg, otherwise we're editing a template/msg
		$add = true;
		if (isset ($cgi->_key) && ! empty ($cgi->_key)) {
			$add = false;
		}
		
		$this->parseSettings ('inc/app/sitemailer2/forms/template/settings.php');
		
		if (! $add) {		
			$rex = new Rex ('sitemailer2_template');
			$document = $rex->getCurrent ($cgi->_key);
			page_title ('SiteMailer 2 - ' . intl_get ('Edit Template'));

		} else {
			list ($set, $tpl) = explode ('/', $cgi->path);
			list ($mode, $name, $ext) = preg_split ('|\.|', basename ($cgi->path));
			page_title ('SiteMailer 2 - ' . intl_get ('New Template'));
		}
		
			
		$this->widgets['top']->data = array ('title' => $document->title, 
											 //'subject' => $document->subject, 
											 'body' => $document->body, 
											 );
		
		//needs {} selector
	}

	function onSubmit ($vals) {
        
        page_onload (false);
		page_onclick (false);
		page_onfocus (false);
        
		loader_import ('cms.Versioning.Rex');
		
		global $cgi;
		
		$rex = new Rex ('sitemailer2_template');	
		
		$data = array ( 'title' => $cgi->title, 
							//'subject' => $cgi->subject, 
							'body' => $cgi->body, 
							'date' => date ('Y-m-d H:i:s'),
						);
			
		
		if (isset ($cgi->_key) && ! empty ($cgi->_key)) {
			$rex->modify ($cgi->_key, $data, 'Updated via template collection...');			
			header ('Location: ' . site_prefix () . '/index/sitemailer2-templates-action?msg=tplsaved');
		} else {
			$rex->create ($data);		
			header ('Location: ' . site_prefix () . '/index/sitemailer2-templates-action?msg=tplcreated');
		}
		exit;
	}
}

?>