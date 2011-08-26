<?php

/**
 * @package XML
 */
class RNGType_int extends RNGType {
	function validate ($value) {
		if (is_numeric ($value)) {
			return true;
		}
		return false;
	}
}

?>