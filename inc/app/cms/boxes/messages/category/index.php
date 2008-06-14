<?php

global $cgi;

loader_import ('cms.Workspace.Message');
loader_import ('cms.Workspace.Message.Filters');
loader_import ('saf.GUI.DataGrid');

$msg = new WorkspaceMessage;

$limit = session_pref ('browse_limit');
if (! $limit) {
	$limit = 10;
}

if (! isset ($cgi->offset) || ! is_numeric ($cgi->offset)) {
	$cgi->offset = 0;
}

if (! isset ($cgi->orderBy) || preg_match ('/[^a-zA-Z0-9_-]/', $cgi->orderBy)) {
	$cgi->orderBy = 'msg_date';
}

if (! isset ($cgi->sort) || ($cgi->sort != 'asc' && $cgi->sort != 'desc')) {
	$cgi->sort = 'desc';
}

if ($cgi->category == 'Sent') {
	$list = $msg->getSent (false, false, $limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
} elseif ($cgi->category == 'Inbox') {
	$list = $msg->getFolder ('', false, $limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
} elseif ($cgi->category == 'Trash') {
	$list = $msg->getTrash ($limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
} else {
	$list = $msg->getFolder ($cgi->category, false, $limit, $cgi->offset, $cgi->orderBy, $cgi->sort);
}

if ($cgi->category == 'Sent') {
	$dg = new DataGrid (
		'sitellite_message',
		array (
			'subject' => intl_get ('Subject'),
			'msg_date' => intl_get ('Date'),
			'recipients' => intl_get ('To'),
		),
		$limit
	);
	$dg->skipHeader ('recipients');
} else {
	$dg = new DataGrid (
		'sitellite_message',
		array (
			'subject' => intl_get ('Subject'),
			'msg_date' => intl_get ('Date'),
			'from_user' => intl_get ('From'),
		),
		$limit
	);
	if ($cgi->category != 'Trash') {
		$dg->setDeleteUrl (site_prefix () . '/index/cms-messages-delete-action');

		$categories = $msg->categories ();
		$dg->footer = template_simple (
			'messages/footer.spt',
			array (
				'list' => $categories,
				'current' => $cgi->category,
			)
		);
	}
}

$dg->primaryKey ('id');
$dg->setEditUrl (site_prefix () . '/index/cms-messages-view-action');
if ($cgi->category == 'Sent') {
	$dg->filter (array (
		'subject' => 'filter_cms_messages_subject',
		'msg_date' => 'filter_cms_messages_date',
		'recipients' => 'filter_cms_messages_to',
	));
} else {
	$dg->filter (array (
		'subject' => 'filter_cms_messages_subject',
		'msg_date' => 'filter_cms_messages_date',
		'from_user' => 'filter_cms_messages_from',
	));
}
$dg->rememberValue ('category', $cgi->category);

$dg->list =& $list;
$dg->total =& $msg->total;

page_title (intl_get ('Folder') . ': ' . $cgi->category);

echo template_simple (CMS_JS_ALERT_MESSAGE, $GLOBALS['cgi']);

echo template_simple ('
	<p>
		<a href="{site/prefix}/index/cms-cpanel-action">{intl Home}</a> &nbsp; &nbsp; &nbsp;
		<a href="{site/prefix}/index/cms-messages-compose-action">{intl Compose Message}</a> &nbsp; &nbsp; &nbsp;
		<a href="{site/prefix}/index/cms-messages-action">{intl Folders}</a><!-- &nbsp; &nbsp;
		<a href="{site/prefix}/index/cms-messages-settings-action">{intl Preferences}</a> &nbsp; &nbsp;
		<a href="{site/prefix}/index/help-app?appname=cms">{intl Help}</a -->
	</p>
');

echo $dg->draw ();

?>