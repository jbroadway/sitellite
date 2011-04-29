<?php
require_once('IXR/Server.php');

class ApiMethodFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */ {
    var $methodName;
    var $inMethodName = FALSE;
    function startElementHandler($tag,$attribs) {
        if ( isset ( $this->child ) ) {
            $this->child->startElementHandler($tag,$attribs);
        }
        if ( $tag == 'methodName' ) {
            $this->inMethodName = TRUE;
        }
    }
    function endElementHandler($tag) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($tag);
        }
        if ( $tag == 'methodName' ) {
            $this->inMethodName = FALSE;
        }
    }
    function characterDataHandler(& $cdata) {
        if ( isset ( $this->child ) ) {
            $this->child->characterDataHandler($cdata);
        }
        if ( $this->inMethodName ) {
            $this->methodName = $cdata;

            // Remap the oldmethod to the new method...
            if ( $cdata == 'myrpcapi.oldmethod' ) {
                $cdata = 'myrpcapi.newmethod';

                $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

                $message = date('Y-M-d H:i:s').": outdated method ".
                    "myrpcapi.oldmethod called by $hostname\n";
                error_log($message,3,'./request.log');
            }
        }
    }
}
class MyRpcAPI extends IXR_Handler {
    function oldMethod() {
        return 'Response from myrpcapi.oldmethod: this method has been deprecited';
    }
    function newMethod() {
        return 'Response from myrpcapi.newmethod: This is the latest version of the API';
    }
}

$handler = & new MyRpcAPI();
$server = & new IXR_Server();
$server->addFilter(new ApiMethodFilter());
$server->addHandler($handler);
$server->serve();
?>