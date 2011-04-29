<?php

if (! session_admin ()) {
	header ('Location: ' . site_prefix () . '/index/news-app');
	exit;
}

class NewsCommentEditForm extends MailForm {
	function NewsCommentEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/news/forms/comment/edit/settings.php');

		page_title (intl_get ('Editing Comment'));

		loader_import ('news.Comment');

		$c = new NewsComment;

		global $cgi;

		$comment = $c->get ($cgi->id);

		$this->widgets['subject']->setValue ($comment->subject);
		$this->widgets['user_id']->setValue ($comment->user_id);
		$this->widgets['body']->setValue ($comment->body);
		$this->widgets['story_id']->setValue ($comment->story_id);

		page_add_script ('
			function news_cancel (f) {
				window.location.href = "' . site_prefix () . '/index/news-app/story.' . $cgi->story_id . '";
				return false;
			}
		');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return news_cancel (this.form)"';
	}

	function onSubmit ($vals) {
		loader_import ('news.Comment');

		$c = new NewsComment;

		$vals['ts'] = date ('Y-m-d H:i:s');
		unset ($vals['submit_button']);

		$c->modify ($vals['id'], $vals);

		page_title (intl_get ('Comment Updated'));
		echo template_simple ('comment_updated.spt', $vals);
	}
}

?>