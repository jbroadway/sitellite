<?php

if (! session_admin ()) {
    header ('Location: ' . site_prefix () . '/index');
    exit;
}

class SiteblogPropertiesForm extends MailForm {
	function SiteblogPropertiesForm () {
		parent::MailForm ();

        global $cgi;
        
        if (empty ($cgi->blog)) return;
        
	$this->parseSettings ('inc/app/siteblog/forms/properties/settings.php');

	page_title ('Editing Category Properties: ' . $cgi->blog);

        $category = db_single ('select * from siteblog_category where id = ?', $cgi->blog);
        
        $set = array ();
        
        if ($category->comments == 'on') {
            $set[] = 'Enable Comments';
        }
        
        if ($category->poster_visible == 'yes') {
            $set[] = 'Author Visible';
        }
        
        if ($category->display_rss == 'yes') {
            $set[] = 'Include RSS Links';
        }
        
        if ($category->status == 'on') {
            $set[] = 'Enabled'; 
        }
        
        $this->widgets['blog_properties']->setValue ($set);
        
        $this->widgets['blog']->setValue ($cgi->blog);
        
        $this->widgets['refer']->setValue ($_SERVER['HTTP_REFERER']);
	}

	function onSubmit ($vals) {
		
        $props = explode (',', $vals['blog_properties']);
        
        $found = false;
        
        foreach ($props as $p) {
            if ($p == 'Enable Comments') {
                db_execute ('update siteblog_category set comments = "on" where id = ?', $vals['blog']);
                $found = true;
            }
        }
        
        if (! $found) {
            db_execute ('update siteblog_category set comments = "off" where id = ?', $vals['blog']);
        }
        
        $found = false;
        
        foreach ($props as $p) {
            if ($p == 'Author Visible') {
                db_execute ('update siteblog_category set poster_visible = "yes" where id = ?', $vals['blog']);
                $found = true;
            }
        }   
        
        if (! $found) {
            db_execute ('update siteblog_category set poster_visible = "no" where id = ?', $vals['blog']);
        }
        
        $found = false;
        
        foreach ($props as $p) {
            if ($p == 'Include RSS Links') {
                db_execute ('update siteblog_category set display_rss = "yes" where id = ?', $vals['blog']);
                $found = true;
            }
        }   
        
        if (! $found) {
            db_execute ('update siteblog_category set display_rss = "no" where id = ?', $vals['blog']);
        }
        
        $found = false;
        
        foreach ($props as $p) {
            if ($p == 'Enabled') {
                db_execute ('update siteblog_category set status = "on" where id = ?', $vals['blog']);
                $found = true;
            }
        }   
        
        if (! $found) {
            db_execute ('update siteblog_category set status = "off" where id = ?', $vals['blog']);
        }
        
        header ('Location: ' . $vals['refer']);
        exit;
        
	}
}

?>
