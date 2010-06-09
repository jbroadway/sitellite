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
* Define path and include PEAR::XML_SaxFilters
*/
if ( !defined('XML_SAXFILTERS') ) {
    define('XML_SAXFILTERS', 'XML/');
}
require_once(XML_SAXFILTERS.'XML_SaxFilters.php');
if ( !defined('IXR_PARSER') ) {
    define('IXR_PARSER', 'Expat');
}
/**
* Represents a single XML-RPC value
* @package IXR
* @access public
*/
class IXR_Value {
    /**
    * Contents of value
    * @var mixed
    * @access private
    */
    var $data;
    /**
    * String identifying XML-RPC value type
    * @var string
    * @access private
    */
    var $type;
    /**
    * @param contents of XML-RPC value
    * @param type (optional) default false
    */
    function IXR_Value ($data, $type = false) {
        $this->data = $data;
        if (!$type) {
            $type = $this->calculateType();
        }
        $this->type = $type;
        if ($type == 'struct') {
            /* Turn all the values in the array in to new IXR_Value objects */
            foreach ($this->data as $key => $value) {
                $this->data[$key] = new IXR_Value($value);
            }
        }
        if ($type == 'array') {
            for ($i = 0, $j = count($this->data); $i < $j; $i++) {
                $this->data[$i] = new IXR_Value($this->data[$i]);
            }
        }
    }
    /**
    * Calculates the type by examining the data
    * @access private
    * @return string name of type
    */
    function calculateType() {
        if ($this->data === true || $this->data === false) {
            return 'boolean';
        }
        if (is_integer($this->data)) {
            return 'int';
        }
        if (is_double($this->data)) {
            return 'double';
        }
        // Deal with IXR object types base64 and date
        if (is_object($this->data) && is_a($this->data, 'IXR_Date')) {
            return 'date';
        }
        if (is_object($this->data) && is_a($this->data, 'IXR_Base64')) {
            return 'base64';
        }
        // If it is a normal PHP object convert it in to a struct
        if (is_object($this->data)) {
            
            $this->data = get_object_vars($this->data);
            return 'struct';
        }
        if ( is_null($this->data) ) {
            return 'nil';
        }
        if (!is_array($this->data)) {
            return 'string';
        }
        /* We have an array - is it an array or a struct ? */
        if ($this->isStruct($this->data)) {
            return 'struct';
        } else {
            return 'array';
        }
    }
    /**
    * Returns the value represented as XML
    * @return string XML representation of value
    * @access public
    */
    function getXml() {
        /* Return XML for this value */
        switch ($this->type) {
            case 'boolean':
                return '<boolean>'.(($this->data) ? '1' : '0').'</boolean>';
                break;
            case 'int':
                return '<int>'.$this->data.'</int>';
                break;
            case 'double':
                return '<double>'.$this->data.'</double>';
                break;
            case 'nil':
                return '<nil/>';
                break;
            case 'string':
                return '<string>'.htmlspecialchars($this->data).'</string>';
                break;
            case 'array':
                $return = '<array><data>'."\n";
                foreach ($this->data as $item) {
                    $return .= '  <value>'.$item->getXml()."</value>\n";
                }
                $return .= '</data></array>';
                return $return;
                break;
            case 'struct':
                $return = '<struct>'."\n";
                foreach ($this->data as $name => $value) {
                    $return .= "  <member><name>$name</name><value>";
                    $return .= $value->getXml()."</value></member>\n";
                }
                $return .= '</struct>';
                return $return;
                break;
            case 'date':
            case 'base64':
                return $this->data->getXml();
                break;
        }
        return false;
    }
    /**
    * Helper function to examine an array and work out if it's an XML-RPC
    * struct
    * @return boolean TRUE if it's a struct
    * @access private
    */
    function isStruct($array) {
        /* Nasty function to check if an array is a struct or not */
        $expected = 0;
        foreach ($array as $key => $value) {
            if ((string)$key != (string)$expected) {
                return true;
            }
            $expected++;
        }
        return false;
    }
}
/**
* Sax filter to parse XML-RPC documents
* @access protected
* @package IXR
*/
class IXR_Filter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */ {
    /**
    * Type of XML-RPC message: methodCall / methodResponse / fault
    * @var string
    * @access private
    */
    var $messageType;
    /**
    * Name of XML-RPC method (request documents only)
    * @var string
    * @access private
    */
    var $methodName;
    /**
    * XML-RPC parameters as PHP data types
    * @var array
    * @access private
    */
    var $params;
    /**
    * The stack used to keep track of the current array/struct
    * @var array
    * @access private
    */
    var $_arraystructs = array();
    /**
    * Stack keeping track of if things are structs or array
    * @var array
    * @access private
    */
    var $_arraystructstypes = array();
    /**
    * A stack as well
    * @var array
    * @access private
    */
    var $_currentStructName = array();
    /**
    * The current tag being handled
    * @var string tag name
    * @access private
    */
    var $_currentTag;
    /**
    * Temporary store for tag contents between characterDataHandler and endElementHandler
    * calls
    * @var string tag name
    * @access private
    */
    var $_currentTagContents;
    /**
    * SAX open handler
    * @param string tag name
    * @param array attributes
    * @return void
    */
    function startElementHandler(& $tag,& $attribs) {
        if ( isset ( $this->child ) ) {
            $this->child->startElementHandler($tag,$attribs);
        }
        $this->_currentTag = $tag;
        switch($tag) {
            case 'methodCall':
            case 'methodResponse':
            case 'fault':
                $this->messageType = $tag;
                break;
            /* Deal with stacks of arrays and structs */
            case 'data':    // data is to all intents and puposes more interesting than array
                $this->_arraystructstypes[] = 'array';
                $this->_arraystructs[] = array();
                break;
            case 'struct':
                $this->_arraystructstypes[] = 'struct';
                $this->_arraystructs[] = array();
                break;
        }
    }
    /**
    * SAX close handler (also supports nil values)
    * @param string tag name
    * @return void
    */
    function endElementHandler(& $tag) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($tag);
        }
        $valueFlag = false;
        switch($tag) {
            case 'int':
            case 'i4':
                $value = (int)trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'double':
                $value = (double)trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'string':
                $value = (string)trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'nil':
                $value = NULL;
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'dateTime.iso8601':
                $value = new IXR_Date(trim($this->_currentTagContents));
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'value':
                // "If no type is indicated, the type is string."
                if (trim($this->_currentTagContents) != '') {
                    $value = (string)$this->_currentTagContents;
                    $this->_currentTagContents = '';
                    $valueFlag = true;
                }
                break;
            case 'boolean':
                $value = (boolean)trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            case 'base64':
                $value = base64_decode($this->_currentTagContents);
                $this->_currentTagContents = '';
                $valueFlag = true;
                break;
            /* Deal with stacks of arrays and structs */
            case 'data':
            case 'struct':
                $value = array_pop($this->_arraystructs);
                array_pop($this->_arraystructstypes);
                $valueFlag = true;
                break;
            case 'member':
                array_pop($this->_currentStructName);
                break;
            case 'name':
                $this->_currentStructName[] = trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                break;
            case 'methodName':
                $this->methodName = trim($this->_currentTagContents);
                $this->_currentTagContents = '';
                break;
        }
        if ($valueFlag) {
            if (count($this->_arraystructs) > 0) {
                // Add value to struct or array
                if ($this->_arraystructstypes[count($this->_arraystructstypes)-1] == 'struct') {
                    // Add to struct
                    $this->_arraystructs[count($this->_arraystructs)-1][$this->_currentStructName[count($this->_currentStructName)-1]] = $value;
                } else {
                    // Add to array
                    $this->_arraystructs[count($this->_arraystructs)-1][] = $value;
                }
            } else {
                // Just add as a paramater
                $this->params[] = $value;
            }
        }
    }
    /**
    * SAX character data handler
    * @param string contents of tag
    * @return void
    */
    function characterDataHandler(& $cdata) {
        if ( isset ( $this->child ) ) {
            $this->child->characterDataHandler($cdata);
        }
        $this->_currentTagContents .= $cdata;
    }
}
/**
* Represents a single XML-RPC message (either request / response or fault)
* @access protected
* @package IXR
*/
class IXR_Message {
    /**
    * Array of XML_SaxFilters
    * @var array
    * @access private
    */
    var $filters = array();
    /**
    * An XML-RPC request or response as raw XML
    * @var string
    * @access private
    */
    var $xml;
    /**
    * Type of XML-RPC message: methodCall / methodResponse / fault
    * @var string
    * @access private
    */
    var $messageType;
    /**
    * XML-RPC method name (if it's a request)
    * @var string
    * @access private
    */
    var $methodName;
    /**
    * XML-RPC parameters as PHP types
    * @var mixed
    * @access private
    */
    var $params;
    /**
    * Fault code placed here (if it's a fault)
    * @var string
    * @access private
    */
    var $faultCode;
    /**
    * Fault string placed here (if it's a fault)
    * @var string
    * @access private
    */
    var $faultString;
    /**
    * Instance of a PEAR::XML_SaxFilters parser
    * @var object subclass of XML_SaxFilters_AbstractParser
    * @access private
    */
    var $_parser;
    /**
    * @param XML-RPC message as raw XML
    * @access protected
    */
    function IXR_Message ($xml) {
        $this->xml = $xml;
    }
    /**
    * Adds an array of sax filters - called from IXR_Client or IXR_Server
    * @param array
    * @return void
    * @access protected
    */
    function addFilters(&$filters) {
        $this->filters=&$filters;
    }
    /**
    * Parse the XML into native PHP types
    * @return void
    * @access protected
    */
    function parse() {
        // first remove the XML declaration
        $this->xml = preg_replace('/<\?xml(.*)?\?'.'>/', '', $this->xml);
        if (trim($this->xml) == '') {
            return false;
        }
        $this->_parser = & XML_SaxFilters_createParser(IXR_PARSER,'String',$this->xml);
        $filter = & new IXR_Filter();
        $this->_parser->setChild($filter);
        $start = TRUE;
        foreach ( $this->filters as $key => $junk ) {
            if ( $start ) {
                $filter->setChild($this->filters[$key]);
                $start = FALSE;
            } else {
                $this->filters[$key-1]->setChild($this->filters[$key]);
            }
        }
        if ( PEAR::isError($result = $this->_parser->parse()) ) {
            $Errors = &IXR_Errors::instance();
            $Errors->add(new IXR_Error(-32700,$result->getMessage()));
        }
        $this->methodName = $filter->methodName;
        $this->params = $filter->params;
        $this->messageType = $filter->messageType;
        if ( $this->messageType == 'fault' ) {
            $this->faultCode = $this->params[0]['faultCode'];
            $this->faultString = $this->params[0]['faultString'];
        }
    }
}
/**
* Handles XML-RPC dates
* @access protected
* @package IXR
*/
class IXR_Date {
    /**
    * @var string
    * @access private
    */
    var $year;
    /**
    * @var string
    * @access private
    */
    var $month;
    /**
    * @var string
    * @access private
    */
    var $day;
    /**
    * @var string
    * @access private
    */
    var $hour;
    /**
    * @var string
    * @access private
    */
    var $minute;
    /**
    * @var string
    * @access private
    */
    var $second;
    /**
    * @param mixed time (ISO or Unix timestamp)
    * @access protected
    */
    function IXR_Date($time) {
        // $time can be a PHP timestamp or an ISO one
        if (is_numeric($time)) {
            $this->parseTimestamp($time);
        } else {
            $this->parseIso($time);
        }
    }
    /**
    * Parses a Unix timestamp into human date units
    * @param int timestamp
    * @return void
    * @access private
    */
    function parseTimestamp($timestamp) {
        $this->year = date('Y', $timestamp);
        $this->month = date('Y', $timestamp);
        $this->day = date('Y', $timestamp);
        $this->hour = date('H', $timestamp);
        $this->minute = date('i', $timestamp);
        $this->second = date('s', $timestamp);
    }
    /**
    * Parses a ISO date into human date units
    * @param string ISO date
    * @return void
    * @access private
    */
    function parseIso($iso) {
        $this->year = substr($iso, 0, 4);
        $this->month = substr($iso, 4, 2); 
        $this->day = substr($iso, 6, 2);
        $this->hour = substr($iso, 9, 2);
        $this->minute = substr($iso, 12, 2);
        $this->second = substr($iso, 15, 2);
    }
    /**
    * Builds an ISO date string from the stored time
    * @return string
    * @access protected
    */
    function getIso() {
        return $this->year.$this->month.$this->day.'T'.$this->hour.':'.$this->minute.':'.$this->second;
    }
    /**
    * Builds a Unix timestamp from the stored time
    * @return int
    * @access protected
    */
    function getTimestamp() {
        return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
    }
    /**
    * Returns an ISO date wrapped in the correct XML-RPC tag
    * @return string
    * @access protected
    */
    function getXml() {
        return '<dateTime.iso8601>'.$this->getIso().'</dateTime.iso8601>';
    }
}

/**
* Handles XML-RPC base64 values
* @access protected
* @package IXR
*/
class IXR_Base64 {
    /**
    * Binary Data to encode
    * @var string
    * @access private
    */
    var $data;
    /**
    * @param string binary Data to encode
    * @access protected
    */
    function IXR_Base64($data) {
        $this->data = $data;
    }
    /**
    * @return string base64 encoded data, wrapped in correct XML-RPC tag
    * @access protected
    */
    function getXml() {
        return '<base64>'.base64_encode($this->data).'</base64>';
    }
}
/**
* Represents and XML-RPC fault
* @access public
* @package IXR
*/
class IXR_Error {
    /**
    * Error code
    * @see http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
    * @var int
    * @access private
    */
    var $code;
    /**
    * Error message
    * @var string
    * @access private
    */
    var $message;
    /**
    * @param int error code
    * @param string error mesage
    * @access protected
    */
    function IXR_Error($code, $message) {
        $this->code = $code;
        $this->message = $message;
    }
    /**
    * Returns the error as an XML-RPC fault document
    * @return string
    * @access protected
    */
    function getXml() {
        $xml = <<<EOD
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>{$this->code}</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>{$this->message}</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse> 

EOD;
        return $xml;
    }
    /**
    * Get the error code
    * @return int
    * @access public
    */
    function code() {
        return $this->code;
    }
    /**
    * Get the error message
    * @return string
    * @access public
    */
    function message() {
        return $this->message;
    }
}
/**
* Singleton collection for placing error messages into
* @access public
* @package IXR
*/
class IXR_Errors {
    /**
    * Array of IXR_Error objects
    * @var array
    * @access private
    */
    var $errors = array();
    /**
    * Add an error to the collection
    * @var IXR_Error
    * @access protected
    */
    function add(&$Error) {
        $this->errors[]=&$Error;
    }
    /**
    * Iterator for fetching errors
    * @return IXR_Error or false when no more errors
    * @access public
    */
    function & fetch() {
        $error = each ($this->errors);
        if ( $error ) {
            return $error['value'];
        } else {
            reset($this->errors);
            return FALSE;
        }
    }
    /**
    * Check to see if there are errors
    * @return boolean TRUE if there are errors
    * @access public
    */
    function isError() {
        if ( count($this->errors) == 0 ) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
    * Returns a static instance of this class
    * @return IXR_Errors
    * @access public
    * @static
    */
    function &instance() {
        static $instance = FALSE;
        if (!$instance) {
            $instance = new IXR_Errors();
        }
        return $instance;
    }
}
/**
* Singleton for storing debug strings
* @access public
* @package IXR
*/
class IXR_Debug {
    /**
    * String containing debug info
    * @var string
    * @access private
    */
    var $debugString = '';
    /**
    * Add more to the debug info
    * @param string info to add
    * @return void
    * @access protected
    */
    function add($msg) {
        $this->debugString.=$msg;
    }
    /**
    * Returns the debug information
    * @return string
    * @access public
    */
    function toString() {
        return $this->debugString;
    }
    /**
    * Returns a static instance of this class
    * @return IXR_Debug
    * @access public
    * @static
    */
    function &instance() {
        static $instance = FALSE;
        if (!$instance) {
            $instance = new IXR_Debug();
        }
        return $instance;
    }
}
?>