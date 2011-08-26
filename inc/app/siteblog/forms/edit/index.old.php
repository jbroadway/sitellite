<?php

if (! session_admin () && session_role () != 'member') {
    header ('Location: ' . site_prefix () . '/index/ihome');
    exit;
}

class SiteblogEditForm extends MailForm {
	function SiteblogEditForm () {
		parent::MailForm ();

        global $cgi;
        
        $refer = $_SERVER['HTTP_REFERER'];
        
		$this->parseSettings ('inc/app/siteblog/forms/edit/settings.php');

        $this->widgets['refer']->setValue ($refer);
        
        //if add is true, we're creating a blog post, otherwise we're editing a blog post
		
        $add = (isset ($cgi->_key) && ! empty($cgi->_key)) ? false : true;
        
        $this->widgets['status']->setValues (array ('Live', 'Not Live'));
        
        $cats = db_pairs ('select id, title from siteblog_category where status = "on"');
        
        if ($add) {

            page_title ('Adding a Blog Post');
            $this->widgets['author']->setValue (session_username ());
            
            unset($this->widgets['icategory']);
            $this->widgets['category']->setValues ($cats);
        

        } else {
            
            loader_import ('cms.Versioning.Rex');
            $rex = new Rex ('siteblog_post');
            $document = $rex->getCurrent ($cgi->_key);
            
            page_title ('Editing a Blog Post');
            //populate fields
            $this->widgets['subject']->setValue ($document->subject);
            $this->widgets['author']->setValue ($document->author);
            $this->widgets['status']->setValue ($document->status);
            unset($this->widgets['category']);

            $catname = db_shift ('select title from siteblog_category where id = ?', $document->category);
            $this->widgets['icategory']->setValue ($catname);
            $this->widgets['oldcat']->setValue ($document->category);
            $this->widgets['body']->setValue ($document->body);
            
        }
	}

	function onSubmit ($vals) {
		
        global $cgi;
        
        if ($vals['submit_buttons'] == 'Cancel') {
            header ('Location: ' . $vals['refer']);
            exit;
        }
        
        loader_import ('cms.Versioning.Rex');
        $rex = new Rex ('siteblog_post');
        
        $id       = $cgi->_key;
        $subject  = $vals['subject'];
        $author   = $vals['author'];
        $status   = ($vals['status'] == 1) ? 'not visible' : 'visible';
        if (empty ($vals['category'])) $vals['category'] = $vals['oldcat'];
        $category = $vals['category'];
        $body     = $vals['body'];
        
        $data = array (  'subject' => $subject,
                         'author'  => $author,
                         'status'  => $status,
                         'category'=> $category, 
                         'body'    => $body);
                         
        if (! empty ($id)) {
            $method = $rex->determineAction ($id);
            $rex->{$method} ($id, $data);
        } else {
            $data['created'] = date ('Y-m-d H:i:s');
            $id = $rex->create ($data);
        }
        
        //view post
        header ('Location: ' . site_prefix () . '/index/siteblog-view-action?id=' . $id);
        exit;
	}
}

?>

