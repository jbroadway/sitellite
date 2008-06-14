<?php

class XedBookmarksAddForm extends MailForm {
	function XedBookmarksAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/bookmarks/add/settings.php');
		page_title (intl_get ('Add Bookmark'));
	}
	function onSubmit ($vals) {
		db_execute (
			'insert into xed_bookmarks (id, name, url) values (null, ?, ?)',
			$vals['name'],
			$vals['url']
		);
		header ('Location: ' . site_prefix () . '/index/xed-bookmarks-action');
		exit;
	}
}

?>