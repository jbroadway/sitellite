<?php

class SitepollCommentAddForm extends MailForm {
	function SitepollCommentAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitepoll/forms/comment/add/settings.php');

		page_title (intl_get ('Add Comment'));

		if (session_valid ()) {
			$this->widgets['user_id']->setDefault (session_username ());
		}

		global $cgi;

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

		$c->add ($vals);

		$ce = appconf ('comments_email');
		if ($ce) {
			@mail ($ce, intl_get ('Poll Comment Notice'), template_simple ('comment_email.spt', $vals), 'From: ' . 'sitepoll@' . site_domain ());
		}

		page_title (intl_get ('Comment Added'));
		echo template_simple ('comment_added.spt', $vals);
	}
}

?>