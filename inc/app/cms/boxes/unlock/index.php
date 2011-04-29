<?php

global $cgi;

loader_import ('cms.Workflow.Lock');

lock_init ();

lock_remove ($cgi->collection, $cgi->key);

loader_import ('saf.MailForm.Autosave');

$a = new Autosave ();

$a->clear ($_SERVER['HTTP_REFERER']);

header ('Location: ' . $cgi->return);

exit;

?>