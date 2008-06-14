<?php

class NewsCommentAddForm extends MailForm {
	function NewsCommentAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/news/forms/comment/add/settings.php');

		page_title (intl_get ('Add Comment'));

		if (session_valid ()) {
			$this->widgets['user_id']->setDefault (session_username ());
		}

		global $cgi;

		page_add_script ('
			function news_cancel (f) {
				window.location.href = "' . site_prefix () . '/index/news-app/story.' . $cgi->story_id . '";
				return false;
			}
		');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return news_cancel (this.form)"';

		if (! appconf ('comments_security')) {
			unset ($this->widgets['security_test']);
		}
	}

	function onSubmit ($vals) {
		loader_import ('news.Comment');

		$c = new NewsComment;

		$vals['ts'] = date ('Y-m-d H:i:s');
		unset ($vals['submit_button']);

		unset ($vals['security_test']);

		$c->add ($vals);

		$ce = appconf ('comments_email');
		if ($ce) {
			loader_import ('news.Functions');
			@mail ($ce, intl_get ('News Comment Notice'), template_simple ('comment_email.spt', $vals), 'From: ' . 'news@' . site_domain ());
		}

		page_title (intl_get ('Comment Added'));
		echo template_simple ('comment_added.spt', $vals);
	}
}

?>