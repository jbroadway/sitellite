<?php

global $cgi;
page_title (intl_get ('Editing Folder') . ': ' . $cgi->category);

class CmsMessagesCategoryEditForm extends MailForm {
	function CmsMessagesCategoryEditForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/cms/forms/messages/category/edit/settings.php');

		global $cgi;
		$this->widgets['name']->setDefault ($cgi->category);
	}
	function onSubmit ($vals) {
		loader_import ('cms.Workspace.Message');
		$msg = new WorkspaceMessage ();
		$msg->renameCategory ($vals['name'], $vals['category']);
		header ('Location: ' . site_prefix () . '/index/cms-messages-action');
		exit;
	}
}

?>