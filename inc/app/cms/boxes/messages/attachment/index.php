<?php

global $cgi;

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

$res = $msg->get ($cgi->message_id);

if (! $res) {
	echo '<script language="javascript" type="text/javascript"> window.close (); </script>';
	exit;
}

$attachment = false;

foreach ($res->attachments as $a) {
	if ($a->id == $cgi->id) {
		$attachment =& $a;
	}
}

if (! $attachment) {
	echo '<script language="javascript" type="text/javascript"> window.close (); </script>';
	exit;
}

if ($cgi->save == 'true') {
	header ('Content-Type: application/x-octet-stream');
	header ('Content-Disposition: attachment; filename=' . $attachment->name);
} else {
	header ('Content-Type: ' . $attachment->mimetype);
}

echo $attachment->body;

exit;

?>