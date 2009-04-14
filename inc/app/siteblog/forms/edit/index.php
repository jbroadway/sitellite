<?php

if (! session_admin ()) {
    header ('Location: ' . site_prefix () . '/index');
    exit;
}

loader_import ('siteblog.Filters');

class SiteblogEditForm extends MailForm {
	function SiteblogEditForm () {
		parent::MailForm ();

        global $cgi;

        $refer = $_SERVER['HTTP_REFERER'];

		$this->parseSettings ('inc/app/siteblog/forms/edit/settings.php');

        $this->widgets['refer']->setValue ($refer);

        //if add is true, we're creating a blog post, otherwise we're editing a blog post

        $add = (isset ($cgi->_key) && ! empty($cgi->_key)) ? false : true;

        $this->widgets['status']->setValues (array ('not visible' => intl_get ('Draft'), 'visible' => intl_get ('Published')));

        $cats = db_pairs ('select id, title from siteblog_category where status = "on" order by title asc');
        foreach ($cats as $k => $v) {
        	$cats[$k] = intl_get ($v);
        }
		$this->widgets['category']->setValues ($cats);

		$twitter_u = appconf ('twitter');
		$twitter_p = appconf ('twitter_pass');
		if (! empty ($twitter_u) && ! empty ($twitter_p)) {
			$this->widgets['twitter'] =& $this->widgets['twitter']->changeType ('text');
		}

        if ($add) {

            page_title (intl_get ('Adding Blog Post'));
            $this->widgets['author']->setValue (session_username ());
            
            //unset($this->widgets['icategory']);

        } else {

            loader_import ('cms.Versioning.Rex');
            $rex = new Rex ('siteblog_post');
            $document = $rex->getCurrent ($cgi->_key);
            
            page_title (intl_get ('Editing Blog Post') . ': ' . $document->subject);
            //populate fields
            $this->widgets['subject']->setValue ($document->subject);
            $this->widgets['author']->setValue ($document->author);
            $this->widgets['status']->setValue ($document->status);
            $this->widgets['category']->setValue ($document->category);
            $this->widgets['created']->setValue ($document->created);
            //unset($this->widgets['category']);

            //$catname = db_shift ('select title from siteblog_category where id = ?', $document->category);
            //$this->widgets['icategory']->setValue ($catname);
            //$this->widgets['oldcat']->setValue ($document->category);
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
        $status   = $vals['status'];
        $category = $vals['category'];
        $created  = $vals['created'];
        $body     = $vals['body'];
        
        $data = array (  'subject' => $subject,
                         'author'  => $author,
                         'status'  => $status,
                         'category'=> $category, 
                         'created' => $created,
                         'body'    => $body);
                         
        if (! empty ($id)) {
        	if (! $data['created']) {
        		unset ($data['created']);
        	}
            $method = $rex->determineAction ($id);
            $rex->{$method} ($id, $data);
        } else {
        	if (! $data['created']) {
	            $data['created'] = date ('Y-m-d H:i:s');
	        }
            $id = $rex->create ($data);
        }

		session_set ('sitellite_alert', intl_get ('Your item has been saved.'));

        // view post
        if (! empty ($vals['_return'])) {
        	header ('Location: ' . $vals['_return']);
        } else {
	        header ('Location: ' . site_prefix () . '/index/siteblog-post-action/id.' . $id . '/title.' . siteblog_filter_link_title ($subject));
	    }

		// ping blog directories via pingomatic.com
		if ($vals['status'] == 'visible') {
			$host = 'rpc.pingomatic.com';
			$path = '';
			
			$out = template_simple ('ping.spt', $obj);
			
			$len = strlen ($out);
			
			$req = 'POST /' . $path . " HTTP/1.0\r\n";
			$req .= 'User-Agent: Sitellite ' . SITELLITE_VERSION . "/SiteBlog\r\n";
			$req .= 'Host: ' . $host . "\r\n";
			$req .= "Content-Type: text/xml\r\n";
			$req .= 'Content-Length: ' . $len . "\r\n\r\n";
			$req .= $out . "\r\n";
			
			if ($ph = @fsockopen ($host, 80)) {
				@fputs ($ph, $req);
				//echo '<pre>';
				//echo htmlentities ($req);
				while (! @feof ($ph)) {
					$res = @fgets ($ph, 128);
					//echo htmlentities ($res);
				}
				@fclose ($ph);
			}
		}

		// post to twitter
		if (! empty ($vals['twitter']) && $vals['status'] == 'visible') {
			$twitter_u = appconf ('twitter');
			$twitter_p = appconf ('twitter_pass');
			if (! empty ($twitter_u) && ! empty ($twitter_p)) {
				loader_import ('siteblog.Bitly');
				$b = new Bitly;
				$short_link = $b->shorten ('http://' . site_domain () . site_prefix () . '/index/siteblog-post-action/id.' . $id . '/title.' . siteblog_filter_link_title ($subject));

				loader_import ('siteblog.Twitter');
				$t = new twitter;
				$t->username = $twitter_u;
				$t->password = $twitter_p;
				$t->update ($vals['twitter'] . ' ' . $short_link);
			}
		}

        exit;
	}
}

?>