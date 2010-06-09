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
* Defines and includes
*/
if ( !defined('IXR_PATH') ) {
    define('IXR_PATH','IXR/');
}
require_once(IXR_PATH.'Common.php');

if ( !defined('PEAR_HTTP') ) {
    define('PEAR_HTTP','HTTP/');
}
require_once(PEAR_HTTP.'/Request.php');

/**
* Base XML-RPC client
* @package IXR
* @access public
*/
class IXR_Client {
    /**
    * Instance of PEAR::HTTP_Request
    * @var HTTP_Request
    * @access private
    */
    var $HttpClient;
    /**
    * Controls whether debugging is switched on or off
    * @var boolean
    * @access private
    */
    var $debug = FALSE;
    /**
    * Instance of IXR_Message containing XML-RPC response
    * @var IXR_Message
    * @access private
    */
    var $message = FALSE;

    /**
    * Array of PEAR::XML_HTMLSax filters to apply to XML-RPC response
    * @var array
    * @access private
    */
    var $filters = array();
    /**
    * @param HTTP_Request
    * @access public
    */
    function IXR_Client(& $HttpClient) {
        $this->HttpClient = & $HttpClient;
        $this->HttpClient->setMethod('POST');
        $this->HttpClient->addHeader('User-Agent','The Incutio XML-RPC PHP Library');
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
    * Execute an XML-RPC request: accepts N arguments
    * <code>
    * $Client->query('foo.bar','one'); // One string argument
    * $Client->query('foo.bar','one',array('two','three')); // Two arguments
    * </code>
    * @param string method
    * @param mixed arguments
    * @return boolean TRUE on successful call
    * @access public
    */
    function query() {
        $args = func_get_args();
        $method = array_shift($args);
        $Request = new IXR_Request($method, $args);
        $Errors = &IXR_Errors::instance();
        $this->HttpClient->addHeader('Content-Type','text/xml');
        $this->HttpClient->addRawPostData($Request->getXml());
        if ( $this->debug ) {
            $Debug = &IXR_Debug::instance();
            $Debug->add("+++REQUEST+++\r\n\r\n");
            $Debug->add($this->HttpClient->_buildRequest());
        }
        if ( PEAR::isError($this->HttpClient->sendRequest()) ) {
            $Errors->add(new IXR_Error(-32300, $response->getMessage()));
        }
        if ( $this->HttpClient->getResponseCode() != '200' ) {
            $Errors->add(new IXR_Error(-32300, 'Invalid HTTP status code '.
                $this->HttpClient->getResponseCode()));
        }
        if ( $this->debug ) {
            $Debug->add("\r\n\r\n+++RESPONSE+++\r\n\r\n");
            $Debug->add(implode("\r\n",$this->HttpClient->getResponseHeader()));
            $Debug->add($this->HttpClient->getResponseBody());
        }

        $this->message = new IXR_Message($this->HttpClient->getResponseBody());
        if ( !$Errors->isError() ) {
            $this->message->addFilters($this->filters);
            $this->message->parse();
            if ( $Errors->isError() ) {
                return FALSE;
            }
            if ($this->message->messageType == 'fault') {
                $Errors->add(new IXR_Error($this->message->faultCode, $this->message->faultString));
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
    * Return the response data structure
    * @return mixed
    * @access public
    */
    function getResponse() {
        return $this->message->params[0];
    }
}
/**
* Client for performing system.multicall methods
* @access public
* @package IXR
*/
class IXR_Client_Multicall extends IXR_Client {
    /**
    * Array of calls to wrap in a single system.multicall
    * @var array
    * @access private
    */
    var $calls = array();
    /**
    * @param HTTP_Request
    * @access public
    */
    function IXR_Client_Multicall(& $HttpClient) {
        parent::IXR_Client($HttpClient);
    }
    /**
    * Adds a call to the list : accepts N arguments
    * @param string method
    * @param mixed arguments
    * @return void
    * @access public
    */
    function addCall() {
        $args = func_get_args();
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }
    /**
    * Performs the multicall
    * @return boolean TRUE on success
    * @access public
    */
    function query() {
        return parent::query('system.multiCall', $this->calls);
    }
    /**
    * Override parent to flatten the response array by one
    * @return array
    * @access public
    */
    function getResponse() {
        $responses = $this->message->params[0];
        $return = array();
        foreach ( $responses as $response ) {
            $return[] = $response[0];
        }
        return $return;
    }
}
/**
* Represents an XML-RPC request
* @access protected
* @package IXR
*/
class IXR_Request {
    /**
    * XML-RPC method name
    * @var string
    * @access private
    */
    var $method;
    /**
    * XML-RPC method arguments as PHP types
    * @var mixed
    * @access private
    */
    var $args;
    /**
    * XML-RPC request as an XML document
    * @var string
    * @access private
    */
    var $xml;
    /**
    * @param string XML-RPC method name
    * @param mixed PHP data types to become XML-RPC parameters
    * @access protected
    */
    function IXR_Request($method, $args) {
        $this->method = $method;
        $this->args = $args;
        $this->xml = <<<EOD
<?xml version="1.0"?>
<methodCall>
<methodName>{$this->method}</methodName>
<params>

EOD;
        foreach ($this->args as $arg) {
            $this->xml .= '<param><value>';
            $v = new IXR_Value($arg);
            $this->xml .= $v->getXml();
            $this->xml .= "</value></param>\n";
        }
        $this->xml .= '</params></methodCall>';
    }
    /**
    * Returns the constructed XML-RPC request document
    * @return string
    * @access protected
    */
    function getXml() {
        return $this->xml;
    }
}
?>