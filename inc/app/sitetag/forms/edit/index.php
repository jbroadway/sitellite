<?php

global $cgi;
loader_import ('sitetag.Widget.Taginput');


if (! isset ($cgi->url)) {
	page_title (intl_get ('Missing url parameter'));
	echo '<p>' . intl_get ('You cannot call this page directly.') . '</p>';
	echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
	return;
}
if (! isset ($cgi->set)) {
	page_title (intl_get ('Missing set parameter'));
	echo '<p>' . intl_get ('You cannot call this page directly.') . '</p>';
	echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
	return;
}

class SitetagEditForm extends MailForm {
	function SitetagEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitetag/forms/edit/settings.php');

		global $page, $cgi;

		page_title (intl_get ('Editing Tags'));

		page_add_script ('
			function cms_cancel (f) {
				window.location.href = "' . site_prefix () . '/' . $cgi->url . '";
				return false;
			}
		');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return cms_cancel (this.form)"';

		loader_import ('sitetag.TagCloud');

		$tc = new TagCloud ($cgi->set);

		$this->widgets['tags']->setDefault ( $tc->getTagsString ($cgi->url));

	}

	function onSubmit ($vals) {

		loader_import ('sitetag.TagCloud');
		$tc = new TagCloud ($vals['set']);
		$tc->updateItem ($vals['url'], $vals['title'], $vals['description'], $vals['tags']);

		header ('Location: ' . site_prefix () . '/' . $vals['url']);
		exit;
	}
}

?>
