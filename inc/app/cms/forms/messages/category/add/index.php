<?php

page_title (intl_get ('Adding Folder'));

class CmsMessagesCategoryAddForm extends MailForm {
	function CmsMessagesCategoryAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/cms/forms/messages/category/add/settings.php');
	}
	function onSubmit ($vals) {
		loader_import ('cms.Workspace.Message');
		$msg = new WorkspaceMessage ();
		$msg->addCategory ($vals['name']);
		header ('Location: ' . site_prefix () . '/index/cms-messages-action');
		exit;
	}
}

?>