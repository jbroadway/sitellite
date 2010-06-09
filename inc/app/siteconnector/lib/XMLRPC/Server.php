<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteconnector/lib/Ext/PEAR' . $join . ini_get ('include_path'));

define ('IXR_PATH', 'inc/app/siteconnector/lib/Ext/IXR/');

loader_import ('siteconnector.Logger');
loader_import ('siteconnector.Functions');
loader_import ('siteconnector.Service');
loader_import ('siteconnector.Ext.IXR.Server');

/**
 * @package siteconnector
 */
class SiteConnector_XMLRPC_Server extends IXR_Server {
	function call ($methodname, $args) {
		$res =& parent::call ($methodname, $args);

		$logger = new SiteConnector_Logger;

		$logger->logQuery ('xmlrpc', $methodname, $args, $res);

		return $res;
	}
}

function siteconnector_set_object (&$obj, $name) {
	$GLOBALS['siteconnector']->addHandler ($obj);
}

function siteconnector_error ($message) {
	return new IXR_Error (0, $message);
}

function siteconnector_test_error ($res) {
	if (is_object ($res) && get_class ($res) == 'ixr_error') {
		return true;
	}
	return false;
}

?>