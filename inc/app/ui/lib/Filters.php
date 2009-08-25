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

?>
