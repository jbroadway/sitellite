NuSOAP - Web Services Toolkit for PHP

Copyright (c) 2002 NuSphere Corporation

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

If you have any questions or comments, please email or visit the website:

This toolkit is delivered as part of PHPEd IDE by NuSphere and is
distributed with PHPEd and via the nusphere website.   For product
information please see:  http://nusphere.com/products/phpadv.htm
for a white paper that provides more detailed information see:
http://nusphere.com/products/tech_library.htm.

For support, email:
support@nusphere.com

More information is also available at:
http://dietrich.ganx4.com/nusoap

Version: 0.6.1 - bugfix release

WHAT IS NuSOAP?

NuSOAP is a set of classes that allow users to send and receive
SOAP messages. Also included are utility classes for parsing WSDL
files and XML Schemas.

INSTALLATION

Enter this line at the top of your script:

include('/path/to/nusoap.php');

USAGE EXAMPLES:

BASIC SERVER EXAMPLE

<?php

require_once('nusoap.php');
$s = new soap_server;
$s->register('hello');
function hello($name){
	// optionally catch an error and return a fault
	if($name == ''){
    	return new soap_fault('Client','','Must supply a valid name.');
    }
	return "hello $name!";
}
$s->service($HTTP_RAW_POST_DATA);

?>

BASIC CLIENT USAGE EXAMPLE

<?php

require_once('nusoap.php');
$parameters = array('name'=>'dietrich');
$soapclient = new soapclient('http://someSOAPServer.com/hello.php');
echo $soapclient->call('hello',$parameters);

?>

WSDL CLIENT USAGE EXAMPLE

<?php

require_once('nusoap.php');
$parameters = array('dietrich');
$soapclient = new soapclient('http://someSOAPServer.com/hello.wsdl','wsdl');
echo $soapclient->call('hello',$parameters);

?>

PROXY CLIENT USAGE EXAMPLE (only works w/ wsdl)

<?php

require_once('nusoap.php');
$soapclient = new soapclient('http://someSOAPServer.com/hello.wsdl','wsdl');
$soap_proxy = $soapclient->getProxy();
echo $soap_proxy->hello('dietrich');

?>
