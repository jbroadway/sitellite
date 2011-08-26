<?php

/**
 * Checks if there is a notice to show.  Notices are set via:
 * session_set ('sitellite_alert', 'Notice message');
 */
function cms_is_alert () {
	$notice = session_get ('sitellite_alert');
	if ($notice) {
		return true;
	}
	return false;
}

/**
 * Shows a notice (e.g., "Item deleted.") if one is set.
 */
function cms_alert () {
	if (! cms_is_alert ()) {
		return '';
	}
	$notice = session_get ('sitellite_alert');
	session_set ('sitellite_alert', null);
	return template_simple ('alert.spt', array ('msg' => $notice));
}

?>