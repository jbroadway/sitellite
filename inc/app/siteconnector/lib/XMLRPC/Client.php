<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteconnector/lib/Ext/PEAR' . $join . ini_get ('include_path'));

define ('IXR_PATH', 'inc/app/siteconnector/lib/Ext/IXR/');

loader_import ('siteconnector.Functions');
loader_import ('siteconnector.Ext.IXR.Client');

/**
 * @package siteconnector
 */
class SiteConnector_XMLRPC_Client extends IXR_Client {
	function call () {
		$args = func_get_args ();
		$res = call_user_func_array (array (&$this, 'query'), $args);
		if ($res) {
			return $this->getResponse ();
		}
		return $res;
	}
}

function siteconnector_error ($res) {
	$errors =& IXR_Errors::instance ();
	while ($error = $errors->fetch ()) {
		return $error->message ();
	}
	return false;
}

?>