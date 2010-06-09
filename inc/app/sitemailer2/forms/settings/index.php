<?php

//echo template_simple ('settings.spt');

class Sitemailer2SettingsForm extends MailForm {
    
	function Sitemailer2SettingsForm () {
        
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemailer2/forms/settings/settings.php');
		page_title ('SiteMailer 2 - Settings');
        
        $settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
        
        $this->widgets['bounces']->setValue ($settings['disable_subscriber_after_bounces']);
        $this->widgets['registration']->setValue ($settings['subscriber_registration']);
        
        if ($settings['subscriber_email_verification']) {
            $this->widgets['verification']->setValue ('yes');
        } else {
            $this->widgets['verification']->setValue ('no');
        }
        
        if ($settings['unsubscribe_email_verification']) {
            $this->widgets['unsubscribe_email_verification']->setValue ('yes');
        } else {
            $this->widgets['unsubscribe_email_verification']->setValue ('no');
        }
        
        if ($settings['public_archive_of_messages']) {
            $this->widgets['archive']->setValue ('yes');
        } else {
            $this->widgets['archive']->setValue ('no');
        }
        
        if ($settings['rss_subscribers']) {
            $this->widgets['rss']->setValue ('yes');
        } else {
            $this->widgets['rss']->setValue ('no');
        }
        
        if ($settings['confirmation_email_domain']) {
            $this->widgets['confirmation_email_domain']->setValue ($settings['confirmation_email_domain']);
        }
         
        if ($settings['confirmation_from']) {
            $this->widgets['confirmation_from']->setValue ($settings['confirmation_from']);
        }
        
        if ($settings['confirmation_subject']) {
            $this->widgets['confirmation_subject']->setValue ($settings['confirmation_subject']);
        }
        
        if ($settings['mailer_domain']) {
            $this->widgets['mailer_domain']->setValue ($settings['mailer_domain']);
        } else {
            $this->widgets['mailer_domain']->setValue (site_domain ());
        }
        
        if ($settings['email_id']) {
            $this->widgets['email_id']->setValue ($settings['email_id']);
        }
        if ($settings['email_password']) {
            $this->widgets['email_password']->setValue ($settings['email_password']);
        }
        if ($settings['email_port']) {
            $this->widgets['email_port']->setValue ($settings['email_port']);
        }
        if ($settings['email_server']) {
            $this->widgets['email_server']->setValue ($settings['email_server']);
        }
	}
    
	function onSubmit ($vals) {
        
        loader_import ('saf.File');
        loader_import ('saf.Misc.Ini');
        
        $settings = array();
        $settings['disable_subscriber_after_bounces'] = $vals ['bounces'];
        $settings['subscriber_registration'] = $vals ['registration'];
        $settings['subscriber_email_verification'] = $vals ['verification'];
        $settings['public_archive_of_messages'] = $vals ['archive'];
        $settings['rss_subscribers'] = $vals ['rss'];
        $settings['confirmation_email_domain'] = $vals ['confirmation_email_domain'];
        $settings['confirmation_from'] = $vals ['confirmation_from'];
        $settings['confirmation_subject'] = $vals ['confirmation_subject'];
        $settings['unsubscribe_email_verification'] = $vals ['unsubscribe_email_verification'];
        $settings['mailer_domain'] = $vals ['mailer_domain'];
        $settings['email_id'] = $vals ['email_id'];
        $settings['email_password'] = $vals ['email_password'];
        $settings['email_port'] = $vals ['email_port'];
        $settings['email_server'] = $vals ['email_server'];
        
        $r = file_overwrite ('inc/app/sitemailer2/conf/settings2.ini.php', ini_write ($settings));
            
        if (! $r) {
            echo '<p>Error: the settings file is not writable, exiting.</p>';
            exit;
        }
        
		header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=settings');
		exit;
	}
}

?>