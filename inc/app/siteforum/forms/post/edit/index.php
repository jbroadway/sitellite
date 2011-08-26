<?php

if (! session_valid ()) {
	header ('Location: ' . site_prefix () . '/index/siteforum-app');
	exit;
}

if (! session_admin ()) {
	global $cgi;
	$user = db_shift ('select user_id from siteforum_post where id = ?', $cgi->id);
	if ($user != session_username ()) {
		header ('Location: ' . site_prefix () . '/index/siteforum-app');
		exit;
	}
}

class SiteforumPostEditForm extends MailForm {
	function SiteforumPostEditForm () {
		parent::MailForm ();

		page_title (intl_get ('Edit a Post'));
		$this->parseSettings ('inc/app/siteforum/forms/post/edit/settings.php');

		page_add_script ('
			function siteforum_preview (f) {
				t = f.target;
				a = f.action;

				f.target = "_blank";
				f.action = "' . site_prefix () . '/index/siteforum-post-preview-action";
				f.submit ();

				f.target = t;
				f.action = a;
				return false;
			}

			function siteforum_insert_tag (tag) {
				e = document.getElementById ("siteforum-body");
				if (tag == "a") {
					e.value += "<a href=\"http://\"></a>";
				} else {
					e.value += "<" + tag + "></" + tag + ">";
				}
				return false;
			}
		');

		global $cgi;
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return siteforum_preview (this.form)"';
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="history.go (-1); return false"';

		loader_import ('siteforum.Post');
		$p = new SiteForum_Post;
		$post = $p->get ($cgi->id);
		$this->widgets['subject']->setValue ($post->subject);
		$this->widgets['body']->setValue ($post->body);

		if (appconf ('use_wysiwyg_editor')) {
			$this->widgets['body'] =& $this->widgets['body']->changeType ('tinyarea');
			$this->widgets['body']->tinyPathLocation = '';
			$this->widgets['body']->tinyButtons1 = 'bold,italic,underline,justifyleft,justifycenter,justifyright,bullist,numlist,link,unlink,emotions,undo,redo,formatselect';
			$this->widgets['body']->alt = '';
		}
	}

	function onSubmit ($vals) {
		loader_import ('siteforum.Post');
		loader_import ('siteforum.Filters');
		loader_import ('siteforum.Topic');

		$p = new SiteForum_Post;

		if (! $p->modify (
			$vals['id'],
			array (
				'subject' => $vals['subject'],
				'body' => $vals['body'],
			)
		)) {
			page_title (intl_get ('Database Error'));
			echo '<p>' . intl_get ('An error occurred.  Please try again later.') . '</p>';
			echo '<p>' . intl_get ('Error Message') . ': ' . $p->error . '</p>';
			return;
		}

		$post = $p->get ($vals['id']);

		page_title (intl_get ('Post Updated'));
		echo template_simple ('post_updated.spt', $post);
	}
}

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>