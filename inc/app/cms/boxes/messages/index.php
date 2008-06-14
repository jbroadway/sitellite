<?php

$data = array ();

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

$data['folders'] = $msg->categories ();

$inbox = new StdClass;
$inbox->name = intl_get ('Inbox');
$inbox->count = db_shift ('select count(*) from sitellite_msg_recipient where type = "user" and user = ? and category = ""', session_username ());

$outbox = new StdClass;
$outbox->name = intl_get ('Sent');
$outbox->count = db_shift ('select count(*) from sitellite_message where from_user = ?', session_username ());

$trash = new StdClass;
$trash->name = intl_get ('Trash');
$trash->count = db_shift ('select count(*) from sitellite_msg_recipient where type = "user" and status = "trash" and user = ?', session_username ());

array_unshift ($data['folders'], $trash);
array_unshift ($data['folders'], $outbox);
array_unshift ($data['folders'], $inbox);

echo template_simple (CMS_JS_ALERT_MESSAGE, $GLOBALS['cgi']);

echo template_simple ('messages/index.spt', $data);

?>