<?php

/**
 * @package XML
 */
class RNGType_empty extends RNGType {
	function validate ($value) {
		if (empty ($value)) {
			return true;
		}
		return false;
	}
}

?>