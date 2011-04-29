<?php
/* 
   IXR - The Inutio XML-RPC Library - (c) Incutio Ltd 2002
   Version 1.61 - Simon Willison, 11th July 2003 (htmlentities -> htmlspecialchars)
   Site:   http://scripts.incutio.com/xmlrpc/
   Manual: http://scripts.incutio.com/xmlrpc/manual.php
   Made available under the Artistic License: http://www.opensource.org/licenses/artistic-license.php
*/
/**
* @package IXR
*/
/**
* Includes and defines
*/
if ( !defined('IXR_PATH') ) {
    define('IXR_PATH','IXR/');
}
require_once(IXR_PATH.'Common.php');

/**
* XML-RPC Server
* @package IXR
* @access public
*/
class IXR_Server {
    /**
    * Associative array of XML-RPC method names to corresponding handler object
    * @var array
    * @access private
    */
    var $callbacks = array();
    /**
    * Indexed array of PEAR::XML_SaxFilters
    * @var array
    * @access private
    */
    var $filters = array();
    /**
    * Constructs the server, adding the default system handler
    * @access public
    */
    function IXR_Server() {
        $this->addHandler(new IXR_System());
    }
    /**
    * Adds an XML-RPC method handler
    * @param object subclass of IXR_Handler
    * @return void
    * @access public
    */
    function addHandler(& $handler) {
        $methods = $handler->_getMethods();
        foreach ( $methods as $method => $callback ) {
            $this->callbacks[$method]['handler'] = & $handler;
            $this->callbacks[$method]['callback'] = $callback;
        }
        $handler->_setServer($this);
    }
    /**
    * Returns an XML-RPC method handler
    * @param string XML-RPC methodname
    * @return object subclass of IXR_Handler
    * @access protected
    */
    function & getHandler($methodname) {
        if ( isset ( $this->callbacks[$methodname] ) ) {
            return $this->callbacks[$methodname]['handler'];
        } else {
            return NULL;
        }
    }
    /**
    * Adds a PEAR::XML_SaxFilters filter which is applied to the response XML
    * @param object subclass of XML_SaxFilters_AbstractFilter
    * @return void
    * @access public
    */
    function addFilter(&$Filter) {
        $this->filters[]=&$Filter;
    }
    /**
    * Accesses global HTTP_RAW_POST_DATA to fetch incoming XML-RPC request
    * @return string XML-RPC request document
    * @access private
    */
    function fetchData() {
        global $HTTP_RAW_POST_DATA;
        if (!$HTTP_RAW_POST_DATA) {
            die('XML-RPC server accepts POST requests only.');
        }
        return $HTTP_RAW_POST_DATA;
    }
    /**
    * Instructs the server to begin serving requests
    * @param string (optional) incoming XML-RPC request
    * @return void
    * @access public
    */
    function serve($data = false) {
        $Errors = & IXR_Errors::instance();
        if (!$data) {
            $data = $this->fetchData();
        }
        $message = new IXR_Message($data);
        $message->addFilters($this->filters);
        $message->parse();
        if ( $Errors->isError() ) {
            $this->error($Errors);
        }
        if ($message->messageType != 'methodCall') {
            $Error = IXR_Error_Handler::invalidxmlrpc();
            $Errors->add($Error);
        }
        if ( $Errors->isError() ) {
            $this->error($Errors);
        }
        $result = $this->call($message->methodName, $message->params);
        // Is the result an error?
        if (is_a($result, 'IXR_Error')) {
            $Errors->add($result);
            $this->error($Errors);
        }
        // Encode the result
        $r = new IXR_Value($result);
        $resultxml = $r->getXml();
        // Create the XML
        $xml = <<<EOD
<methodResponse>
  <params>
    <param>
      <value>
        $resultxml
      </value>
    </param>
  </params>
</methodResponse>

EOD;
        // Send it
        $this->output($xml);
    }
    /**
    * Call the handler method corresponding the XML-RPC request
    * @param string method name
    * @param array or request arguments
    * @return mixed response from handler method
    * @access protected
    */
    function call($methodname, $args) {
        if (NULL === ($handler = & $this->getHandler($methodname)) ) {
            return IXR_Error_Handler::methodNotFound($methodname);
        }
        // Perform the callback and send the response
        if (count($args) == 1) {
            // If only one paramater just send that instead of the whole array
            //$args = $args[0];
        }
        // Are we dealing with a function or a method?
        $callback = $this->callbacks[$methodname]['callback'];
        if (!method_exists($handler, $callback)) {
            return IXR_Error_Handler::methodNotFound($callback);
        }
        // Call the method
        return call_user_func_array(array(&$handler,$callback),$args);
    }
    /**
    * Constructs an error response if there's problems
    * @param IXR_Errors instance of error collection
    * @return void
    * @access private
    */
    function error(&$Errors) {
        $error = & $Errors->fetch();
        $this->output($error->getXml());
    }
    /**
    * Sends HTTP headers and displays document
    * @param string XML-RPC response document
    * @return void
    * @access private
    */
    function output($xml) {
        $xml = '<?xml version="1.0"?>'."\n".$xml;
        $length = strlen($xml);
        header('Connection: close');
        header('Content-Length: '.$length);
        header('Content-Type: text/xml');
        header('Date: '.date('r'));
        echo $xml;
        exit;
    }
}

/**
* Collection of static methods for creating XML-RPC errors
* @package IXR
* @access protected
*/
class IXR_Error_Handler {
    /**
    * @return IXR_Error
    * @access protected
    */
    function invalidxmlrpc() {
        return new IXR_Error(-32600,
            'Server error. Invalid xml-rpc. Not conforming to spec. Request must be a methodCall');
    }
    /**
    * @return IXR_Error
    * @access protected
    */
    function methodNotFound($method) {
        return new IXR_Error(-32601,
            'Server error. Requested class method "'.$method.'" does not exist.');
    }
    /**
    * @return IXR_Error
    * @access protected
    */
    function multiCallRecursion() {
        return new IXR_Error(-32600,
            'Recursive calls to system.multicall are forbidden');
    }
    /**
    * @return IXR_Error
    * @access protected
    */
    function noSignature($method) {
        return new IXR_Error(-32603,
            'Server error. Requested method "'.$method.'" has no defined signature.');
    }
    /**
    * @return IXR_Error
    * @access protected
    */
    function noHelp($method) {
        return new IXR_Error(-32603,
            'Server error. Requested method "'.$method.'" has no help defined.');
    }
}
/**
* Base class for XML-RPC handlers
* @package IXR
* @abstract
*/
class IXR_Handler {
    /**
    * Instance of IXR_Server
    * @var IXR_Server
    * @access private
    */
    var $server;
    /**
    * Sets the server, making it available to handlers as needed
    * @param IXR_Server
    * @return void
    * @access protected
    */
    function _setServer(& $server) {
        $this->server = & $server;
    }
    /**
    * Returns the available methods of this handler. Unless overridden, this
    * method uses introspection to determine available methods. Subclass
    * handler methods beginning with an underscore are ignored.
    * @return array
    * @access protected
    */
    function _getMethods () {
        $methods = array();
        $class = get_class($this);
        $classmethods = get_class_methods($this);
        foreach ( $classmethods as $classmethod ) {
            if ( substr($classmethod,0,1) != '_' ) {
                $method = $class.'.'.$classmethod;
                $methods[$method] = $classmethod;
            }
        }
        return $methods;
    }
    /**
    * Return the signatures of the handler methods. Does nothing here -
    * override with your own to define signatures
    * @return array
    * @access protected
    */
    function _getSignatures() {
        return array();
    }
    /**
    * Return the method help of the handler methods. Does nothing here -
    * override with your own to define method help
    * @return array
    * @access protected
    */
    function _getHelp() {
        return array();
    }
}
/**
* Handler for XML-RPC system.* methods
* @package IXR
* @access public
*/
class IXR_System extends IXR_Handler {
    /**
    * @return array
    * @access protected
    */
    function _getMethods () {
        $methods = array (
            'system.methodSignature'=>'methodSignature',
            'system.getCapabilities'=>'getCapabilities',
            'system.listMethods'=>'listMethods',
            'system.methodHelp'=>'methodHelp',
            'system.multiCall'=>'multiCall',
        );
        return $methods;
    }
    /**
    * @return array
    * @access protected
    */
    function _getSignatures() {
        $signatures = array (
            'system.methodSignature' => array(
                    array('out'=>'array','in'=>'string')
                ),
            'system.getCapabilities' => array(
                array('out'=>'struct')
                ),
            'system.listMethods' => array(
                array('out'=>'array')
                ),
            'system.methodHelp' => array(
                array('out'=>'string','in'=>'string')
                ),
            'system.multiCall' => array(
                array('out'=>'array','in'=>'array')
                ),
            );
        return $signatures;
    }
    /**
    * @return array
    * @access protected
    */
    function _getHelp() {
        $help = array (
            'system.methodSignature' => 
                'Returns an array describing the return type and required parameters of a method',
            'system.getCapabilities' => 
                'Returns a struct describing the XML-RPC specifications supported by this server',
            'system.listMethods' => 
                'Returns an array of available methods on this server',
            'system.methodHelp' => 
                'Returns a documentation string for the specified method',
            'system.multiCall' => 
                'Executes multiple methods in sequence and returns the results',
        );
        return $help;
    }
    /**
    * Returns the XML-RPC spec extensions this server supports
    * @see http://groups.yahoo.com/group/xml-rpc/message/2897
    * @return array
    * @access public
    */
    function getCapabilities($args) {
        $capabilities = array(
            'introspection' => array(
                'specUrl' => 'http://xmlrpc.usefulinc.com/doc/reserved.html',
                'specVersion' => 1
            ),
            'xmlrpc' => array(
                'specUrl' => 'http://www.xmlrpc.com/spec',
                'specVersion' => 1
            ),
            'faults_interop' => array(
                'specUrl' => 'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php',
                'specVersion' => 20010516
            ),
            'system.multicall' => array(
                'specUrl' => 'http://www.xmlrpc.com/discuss/msgReader$1208',
                'specVersion' => 1
            ),
            'nil' => array(
                'specUrl' => 'http://ontosys.com/xml-rpc/extensions.html',
                'specVersion' => 20010518
            ),
        );
        return $capabilities;
    }
    /**
    * Allows for method "box-carring".
    * @see http://www.xmlrpc.com/discuss/msgReader$1208
    * @param array of method calls
    * @return array responses from method calls in order they occurred
    * @access public
    */
    function multiCall() {
        $methodcalls = func_get_args();
        // See http://www.xmlrpc.com/discuss/msgReader$1208
        $return = array();
        foreach ($methodcalls as $call) {
            $method = $call['methodName'];
            if ( isset($call['params']) ) {
                $params = $call['params'];
            } else {
                $params = array();
            }
            if ($method == 'system.multiCall') {
                $result = IXR_Error_Hander::multiCallRecursion();
            } else {
                $result = $this->server->call($method, $params);
            }
            if (is_a($result, 'IXR_Error')) {
                $return[] = array(
                    'faultCode' => $result->code(),
                    'faultString' => $result->message()
                );
            } else {
                $return[] = array($result);
            }
        }
        return $return;
    }
    /**
    * Returns the methods available from the server
    * @see http://scripts.incutio.com/xmlrpc/introspection.html
    * @see http://xmlrpc-c.sourceforge.net/xmlrpc-howto/xmlrpc-howto-api-introspection.html
    * @return array of available XML-RPC methods
    * @access public
    */
    function listMethods() {
        $methods = array_keys($this->server->callbacks);
        asort($methods);
        reset($methods);
        $return = array();
        foreach ( $methods as $method ) {
            $return[]=$method;
        }
        return $return;
    }
    /**
    * Returns the signatureS (note plural) for a given method
    * @see http://scripts.incutio.com/xmlrpc/introspection.html
    * @see http://xmlrpc-c.sourceforge.net/xmlrpc-howto/xmlrpc-howto-api-introspection.html
    * @param string XML-RPC method name
    * @return array of available XML-RPC methods
    * @access public
    */
    function methodSignature($method) {
        if (NULL === ($handler = & $this->server->getHandler($method)) ) {
            return IXR_Error_Handler::methodNotFound($method);
        }

        $allsigs = $handler->_getSignatures();
        if ( !isset($allsigs[$method]) ) {
            return IXR_Error_Handler::noSignature($method);
        }
        $signatures = $allsigs[$method];
        return $this->_buildSignatures($signatures);
    }
    /**
    * @param array of signatures for a given method from IXR_Handler::_getSignatures()
    * @return array signatures prepared for XML-RPC response
    * @access private
    */
    function _buildSignatures($signatures) {
        $return = array();
        foreach ( $signatures as $signature ) {
            $sig = array();
            if ( isset($signature['out']) ) {
                $sig[]=$signature['out'];
            }
            if ( isset($signature['in']) ) {
                if ( is_array($signature['in']) ) {
                    foreach ( $signature['in'] as $in ) {
                        $sig[] = $in;
                    }
                } else {
                    $sig[]=$signature['in'];
                }
            }
            $return[]=$sig;
        }
        return $return;
    }
    /**
    * Returns the method help for a given XML-RPC method
    * @see http://scripts.incutio.com/xmlrpc/introspection.html
    * @see http://xmlrpc-c.sourceforge.net/xmlrpc-howto/xmlrpc-howto-api-introspection.html
    * @param string XML-RPC method name
    * @return string method help
    * @access public
    */
    function methodHelp($method) {
        if (NULL === ($handler = & $this->server->getHandler($method)) ) {
            return IXR_Error_Handler::methodNotFound($method);
        }
        $allhelp = $handler->_getHelp();
        if ( !isset($allhelp[$method]) ) {
            return IXR_Error_Handler::noHelp($method);
        }
        return $allhelp[$method];
    }
}
?>