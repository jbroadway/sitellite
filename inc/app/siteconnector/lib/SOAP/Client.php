<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteconnector/lib/Ext/PEAR' . $join . ini_get ('include_path'));

loader_import ('siteconnector.Functions');
loader_import ('siteconnector.Ext.PEAR.SOAP.Client');

/**
 * @package siteconnector
 */
class SiteConnector_SOAP_Client extends SOAP_Client {
	/**
	 * Generates a client object, which can be used transparently
	 * as if it was an ordinary class, based on the specified
	 * WSDL resource.
	 *
	 * @access	public
	 * @param	boolean	use WSDL caching
	 * @param	int		cache max lifetime (in seconds)
	 * @return	object reference
	 */
	function &getObject ($cacheUse = WSDL_CACHE_USE, $cacheMaxAge = WSDL_CACHE_MAX_AGE) {
		$this->wsdl = new SOAP_WSDL ($this->_endpoint, $this->__proxy_params, $cacheUse, $cacheMaxAge);

		if ($this->wsdl->fault) {
			$this->fault = $this->wsdl->fault->getFault ();
			$this->error = $this->fault->faultstring . ' (Code: ' . $this->fault->faultcode . ')';
			return false;
		}

		return $this->wsdl->getProxy ();
	}
}

function siteconnector_error (&$obj) {
	if (is_object ($obj) && get_class ($obj) == 'soap_fault') {
		$fault = $obj->getFault ();
		return $fault->faultstring;
	}
	return false;
}

?>