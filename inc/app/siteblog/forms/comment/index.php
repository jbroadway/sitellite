<?php

loader_import ('siteblog.Filters');

if (db_shift ('select count(*) from siteblog_banned where ip = ?', $_SERVER['REMOTE_ADDR'])) {
	echo '<p><strong>' . intl_get ('Banned IP Address') . ':</strong> Someone using this IP address has previously been banned from posting comments.  Our apologies if your IP address is shared and it wasn\'t you -- sometimes the bad guys can ruin it for everyone.</p>';
	return;
}

class SiteblogCommentForm extends MailForm {
	function SiteblogCommentForm () {
		parent::MailForm ();

        global $cgi;
        
        $this->parseSettings ('inc/app/siteblog/forms/comment/settings.php');
        
        if (isset ($cgi->_key) && ! empty ($cgi->_key)) {
            //edit a comment
            page_title ('Editing Comment');
            
            $comment = db_single ('select * from siteblog_comment where id = ?', $cgi->_key);

			$this->widgets['name']->setValue ($comment->author);
            $this->widgets['email']->setValue ($comment->email);
            $this->widgets['url']->setValue ($comment->url);
            $this->widgets['body']->setValue ($comment->body);
            
        } elseif (! isset ($cgi->post)) {
            
            header ('Location: ' . site_prefix() . '/index');
            exit;
            
        } else {
        
            if (session_valid ()) {
	            $this->widgets['name']->setValue (session_username ());
	            $user = session_get_user ();
	            $this->widgets['email']->setValue ($user->email);
	            $this->widgets['url']->setValue ($user->website);
	        }

            $this->widgets['post']->setValue ($cgi->post);
            //page_title ('Post a Comment');
        }

		if (! appconf ('comments_security')) {
			unset ($this->widgets['security_test']);
		}
	}

	function onSubmit ($vals) {
		$ak = appconf ('akismet_key');
		if ($ak) {
			loader_import ('siteblog.Akismet');
			$comment = array (
				'author' => $vals['name'],
				'email' => $vals['email'],
				'website' => $vals['url'],
				'body' => $vals['body'],
				'permalink' => site_url () . '/index/siteblog-post-action/id.' . $vals['post'] . '/title.' . siteblog_filter_link_title ($title),
				'user_ip' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			);

			$akismet = new Akismet (site_url (), $ak, $comment);

			if (! $akismet->errorsExist ()) {
				// no errors
				if ($akismet->isSpam ()) {
					// akismet says spam
					$title = db_shift ('select subject from siteblog_post where id = ?', $vals['post']);
					db_execute (
						'insert into siteblog_akismet values (null, ?, now(), ?, ?, ?, ?, ?, ?)',
						$vals['post'],
						$comment['author'],
						$comment['email'],
						$comment['website'],
						$comment['user_ip'],
						$comment['user_agent'],
						$comment['body']
					);
					header ('Location: ' . site_prefix () . '/index/siteblog-post-action/id.' . $vals['post'] . '/title.' . siteblog_filter_link_title ($title));
			        exit;
				}
			}
		}

         if (! empty ($vals['post'])) {
        
            $res = db_execute (
            	'insert into siteblog_comment (id, child_of_post, body, date, author, email, url, ip) values (null, ?, ?, now(), ?, ?, ?, ?)',
            	$vals['post'],
            	$vals['body'],
            	$vals['name'],
            	$vals['email'],
            	$vals['url'],
            	$_SERVER['REMOTE_ADDR']
            );
            if (! $res) {
            	die (db_error ());
            }
            $id = db_lastid ();
            
        } else {
            
            $res = db_execute (
            	'update siteblog_comment set body = ?, author = ?, email = ?, url = ? where id = ?',
            	$vals['body'],
            	$vals['name'],
            	$vals['email'],
            	$vals['url'],
            	$vals['_key']
            );
            if (! $res) {
            	die (db_error ());
            }
            $id = $vals['_key'];

            $vals['post'] = db_shift ('select child_of_post from siteblog_comment where id = ?', $vals['_key']);

        }

		$title = db_shift ('select subject from siteblog_post where id = ?', $vals['post']);

        header ('Location: ' . site_prefix () . '/index/siteblog-post-action/id.' . $vals['post'] . '/title.' . siteblog_filter_link_title ($title) . '#siteblog-comment-' . $id);
        exit;
	}
}

?>