<?php

// authenticate clients (http basic)
if (! isset ($_SERVER['PHP_AUTH_USER'])) {
	header ('WWW-Authenticate: Basic realm="SiteConnector"');
	header ('HTTP/1.0 401 Unauthorized');
	echo 'Authorization is required to access this resource.';
	exit;
} else {
	$res = db_shift (
		'select password from sitellite_user where username = ? and role = ?',
		$_SERVER['PHP_AUTH_USER'],
		'siteconnector'
	);
	if (! $res || ! better_crypt_compare ($_SERVER['PHP_AUTH_PW'], $res)) {
		header ('WWW-Authenticate: Basic realm="SiteConnector"');
		header ('HTTP/1.0 401 Unauthorized');
		echo 'Authorization is required to access this resource.';
		exit;
	}
}

// api defaults to soap.  valid api's are soap and xmlrpc
if (! isset ($parameters['api'])) {
	$parameters['api'] = 'soap';
}

// service does not have a default  service is either a class in
// siteconnector.Service or a loader path to another class.

if ($parameters['api'] == 'soap') {
	// soap api handling

	if (preg_match ('/\?wsdl$/i', $_SERVER['REQUEST_URI'])) {
		// display wsdl of specified service

		loader_import ('siteconnector.Functions');

		header ('Content-Type: text/xml');

		echo siteconnector_wsdl (
			'siteconnector.Service.' . ucfirst ($parameters['service']),
			$parameters['service']
		);

	} else {
		// handle soap request

		loader_import ('siteconnector.SOAP.Server');

		$GLOBALS['siteconnector'] = new SiteConnector_SOAP_Server;

		global $siteconnector;

		if (strstr ($parameters['service'], '.')) {
			if (! loader_import ($parameters['service'])) {
				die ('Service not found');
			}
			$classname = 'SiteConnector_Service_';
			preg_match ('/\.([^\.]+)$/', $parameters['service'], $regs);
			$classname .= $regs[1];
		} else {
			if (! loader_import ('siteconnector.Service.' . ucfirst ($parameters['service']))) {
				die ('Service not found');
			}
			$classname = 'SiteConnector_Service_' . $parameters['service'];
		}

		$service = new $classname;

		siteconnector_set_object ($service, $parameters['service']);

		$raw = siteconnector_get_raw_post_data ();

		$siteconnector->service ($raw);
	}

} elseif ($parameters['api'] == 'xmlrpc') {
	// xmlrpc api handling

	loader_import ('siteconnector.XMLRPC.Server');

	$GLOBALS['siteconnector'] = new SiteConnector_XMLRPC_Server;

	global $siteconnector;

	if (strstr ($parameters['service'], '.')) {
		if (! loader_import ($parameters['service'])) {
			die ('Service not found');
		}
		$classname = 'SiteConnector_Service_';
		preg_match ('/\.([^\.]+)$/', $parameters['service'], $regs);
		$classname .= $regs[1];
	} else {
		if (! loader_import ('siteconnector.Service.' . ucfirst ($parameters['service']))) {
			die ('Service not found');
		}
		$classname = 'SiteConnector_Service_' . $parameters['service'];
	}

	$service = new $classname;

	siteconnector_set_object ($service, $parameters['service']);

	$raw = siteconnector_get_raw_post_data ();

	$siteconnector->serve ($raw);

} else {
	echo 'Invalid API';
}

exit;

?>