<?php

if ($context == 'action') {
	loader_import ('saf.Misc.RPC');

	class CMS_Browse_Level {
		function toggle () {
			// switch browse level
			if (session_pref ('browse_level') == 'normal') {
				session_pref_set ('browse_level', 'advanced');
			} else {
				session_pref_set ('browse_level', 'normal');
			}
			return true;
		}
	}

	echo rpc_handle (new CMS_Browse_Level (), $parameters);
	exit;

	// return to whence they came
	//header ('Location: ' . $_SERVER['HTTP_REFERER']);
	//exit;
}

// show their browse level and the switch link
page_add_script (site_prefix () . '/js/rpc.js');
echo template_simple ('user_preferences_level.spt');

?>