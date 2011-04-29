<?php

if (! session_admin ()) {
	header ('Location: ' . site_prefix () . '/index/poll-app');
	exit;
}

class SitepollCommentEditForm extends MailForm {
	function SitepollCommentEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitepoll/forms/comment/edit/settings.php');

		page_title (intl_get ('Editing Comment'));

		loader_import ('sitepoll.Comment');

		$c = new SitepollComment;

		global $cgi;

		$comment = $c->get ($cgi->id);

		$this->widgets['subject']->setValue ($comment->subject);
		$this->widgets['user_id']->setValue ($comment->user_id);
		$this->widgets['body']->setValue ($comment->body);
		$this->widgets['poll']->setValue ($comment->poll);

		page_add_script ('
			function sitepoll_cancel (f) {
				window.location.href = "' . site_prefix () . '/index/sitepoll-results-action/poll.' . $cgi->poll . '";
				return false;
			}
		');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return sitepoll_cancel (this.form)"';
	}

	function onSubmit ($vals) {
		loader_import ('sitepoll.Comment');

		$c = new SitepollComment;

		$vals['ts'] = date ('Y-m-d H:i:s');
		unset ($vals['submit_button']);

		$c->modify ($vals['id'], $vals);

		page_title (intl_get ('Comment Updated'));
		echo template_simple ('comment_updated.spt', $vals);
	}
}

?>