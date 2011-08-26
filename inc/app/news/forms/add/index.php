<?php

class NewsAddForm extends MailForm {
	function NewsAddForm () {
		parent::MailForm ();

		global $page, $cgi;

		$this->parseSettings ('inc/app/news/forms/add/settings.php');

		page_title (intl_get ('Adding News Story'));

		loader_import ('ext.phpsniff');
		loader_import ('saf.Date.Calendar.Simple');
        	loader_import ('saf.Date');        

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		page_add_script ('
			function cms_cancel (f) {
				onbeforeunload_form_submitted = true;
				if (arguments.length == 0) {
					window.location.href = "/index/cms-app";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/news-app";
					}
				}
				return false;
			}
		');
		if (session_pref ('form_help') == 'off') {
			page_add_script ('
				formhelp_disable = true;
			');
		}

		// add cancel handler
		$this->widgets['submit_button']->buttons[0]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="return cms_cancel (this.form)"';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

        // handle thumbnail

        // check if thumb is set
        if( !empty( $vals['thumb'] ) ) {
         // check if thumbnail path ends with "-thumb.jpg"
         $string = substr($vals['thumb'], -10 );
         if($string != "-thumb.jpg") {
          // create a thumbnail

          // get thumb_filename and thumb_dir
          // remove http://
          $path = str_replace("http://", "", $vals['thumb']);

          // split path by /
          $split = split("/", $path);
          // the thumb body is the last item in the array
          $thumb_filename = $split [ count($split) - 1];
          // now get the directory, by deleting last entry...
          unset($split [ count($split) - 1] );
          // the first entry (i.e. the domain)...
          $domain = $split[0];
          unset($split [0] );
          // and implode again
          $dir = implode("/",$split);

          // get thumb_body, i.e. filename without extension
          $split2 = split("\.", $thumb_filename);
          // extension is last part, so delete it and implode again.
          unset($split2 [ count($split2) - 1] );
          $thumb_body = implode(".",$split2);

          $orig_filename = $dir . "/" . $thumb_filename;
          $thumbnail = $dir . "/" . $thumb_body . "-thumb.jpg";

          // load thumbnail class
          loader_import("saf.Ext.thumbnail");

          // get conf. parameters (settings.ini.php)
          $max_width = appconf("thumb_width");
          $max_height = appconf("thumb_height");

          // make the thumbnail
          makethumbnail($orig_filename , $thumbnail , $max_width , $max_height );

          // set $vals['thumb'] to the new filename
          $vals['thumb'] = "http://" . $domain . "/" . $thumbnail;
         }
        }

        // end thumbnail

        $rex = new Rex ($collection);

		$continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab-end']);

		$res = $rex->create ($vals, $changelog);

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $res) {
			if (empty ($return)) {
				$return = site_prefix () . '/index/cms-browse-action?collection=sitellite_news';
			}
			echo loader_box ('cms/error', array (
				'message' => $rex->error,
				'collection' => $collection,
				'key' => $key,
				'action' => $method,
				'data' => $vals,
				'changelog' => $changelog,
				'return' => $return,
			));
		} else {
			loader_import ('cms.Workflow');
			echo Workflow::trigger (
				'add',
				array (
					'collection' => $collection,
					'key' => $key,
					'data' => $vals,
					'changelog' => $changelog,
					'message' => 'Collection: ' . $collection . ', Item: ' . $key . ' (' . $vals['title'] . ')',
				)
			);

			session_set ('sitellite_alert', intl_get ('Your item has been created.'));

                        if ($continue) {
                                header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
                                exit;
                        }

			if ($return) {
				header ('Location: ' . $return);
				exit;
			}
			header ('Location: ' . site_prefix () . '/index/news-app/story.' . $res);
			exit;
		}
	}
}

?>