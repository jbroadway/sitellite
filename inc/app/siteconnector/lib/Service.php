<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteconnector/lib/Ext/PEAR' . $join . ini_get ('include_path'));

define ('IXR_PATH', 'inc/app/siteconnector/lib/Ext/IXR/');

loader_import ('siteconnector.Ext.IXR.Server');

/**
 * @package siteconnector
 */
class SiteConnector_Service extends IXR_Handler {
	function _getMethods () {
		$methods = parent::_getMethods ();
		foreach ($methods as $k => $v) {
			$c = str_replace ('siteconnector_service_', '', $k);
			$methods[$c] = $v;
			unset ($methods[$k]);
		}
		return $methods;
	}
}

?>