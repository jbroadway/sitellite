<?php

loader_import ('saf.Misc.RPC');
loader_import ('saf.MailForm.Autosave');

echo rpc_handle (new Autosave (), $parameters);
exit;

?>