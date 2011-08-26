<?php

loader_import ('saf.Misc.RPC');

echo rpc_init ('
	if (arguments[0] == false) {
		alert ("' . intl_get ('Error adding bookmark!') . '");
	} else {
		alert ("' . intl_get ('Bookmark added.') . '");
	}
');

loader_import ('saf.GUI.Prompt');

echo template_simple ('bookmark_button.spt');

?>