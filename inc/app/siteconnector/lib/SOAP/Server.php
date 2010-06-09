<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteconnector/lib/Ext/PEAR' . $join . ini_get ('include_path'));

loader_import ('siteconnector.Logger');
loader_import ('siteconnector.Functions');
loader_import ('siteconnector.Service');
loader_import ('siteconnector.Ext.PEAR.SOAP.Server');

/**
 * @package siteconnector
 */
class SiteConnector_SOAP_Server extends SOAP_Server {
	function &callMethod ($methodname, &$args) {
		$res =& parent::callMethod ($methodname, $args);

		$logger = new SiteConnector_Logger;

		$logger->logQuery ('soap', $methodname, $args, $res);

		return $res;
	}
}

function siteconnector_set_object (&$obj, $name) {
	$GLOBALS['siteconnector']->addObjectMap ($obj, site_url () . '/index/siteconnector-app/api.soap/service.' . $name, $name);
}

function siteconnector_error ($message) {
	return new SOAP_Fault ($message);
}

function siteconnector_test_error (&$obj) {
	if (is_object ($obj) && get_class ($obj) == 'soap_fault') {
		return true;
	}
	return false;
}

?>