<?php

/**
 * Temp compatibility function before Sitellite 5.1
 */
if (! function_exists ('intl_datetime')) {
function intl_datetime ($date) {

	$ts = strtotime ($date);
	return date ('M d, Y g:i A', $ts);

}
}


function ui_website_filter ($website) {
	$website = trim (strip_tags ($website));
	if (empty ($website)) {
		return '';
	}
	if (strpos ($website, 'http') !== 0) {
		if ($website[0] == '/') {
			$website = substr ($website, 1);
		}
		$website = 'http://' . $website;
	}
	return $website;
}

?>
