<?php
set_time_limit(0);
require_once('IXR/Client.php');
require_once ('HTML/Progress.php');

class ProgressFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */ {
    var $bar;
    var $count;
    var $div;
    function ProgressFilter(&$bar) {
        $this->bar = & $bar;
        $this->count = 1;
        $this->setDiv(1000);
    }
    function setDiv($max) {
        $this->div = ceil($max / 10 );
    }
    function startElementHandler($tag,$attribs) {
        if ( isset ( $this->child ) ) {
            $this->child->startElementHandler($tag,$attribs);
        }
        if ( $tag == 'string' ) {
            if ( $this->count % $this->div == 0 ) {
                $this->bar->incValue(10);
                $this->bar->display();
            }
            $this->count++;
        }
    }
    function endElementHandler($tag) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($tag);
        }
    }
    function characterDataHandler($cdata) {
        if ( isset ( $this->child ) ) {
            $this->child->characterDataHandler($cdata);
        }
    }
}

$bar = new HTML_Progress();
$bar->setIncrement(10);
$ui =& $bar->getUI();

$request = & new HTTP_Request('http://localhost/ixr/docs/examples/longwait_server.php');
$client = new IXR_Client($request);

if (!$client->query('longwait.getcount') ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    $count = $client->getResponse();
}

$progFilter = & new ProgressFilter($bar);
$progFilter->setDiv($count);
$client->addFilter($progFilter);

// $client->debug = TRUE;
?>
<html>
<head>
<title>Client with Progress Bar, using SAX Filter</title>
<style type="text/css">
<!--
<?php echo $bar->getStyle(); ?>
// -->
</style>
<script type="text/javascript">
<!--
<?php echo $ui->getScript(); ?>
//-->
</script>
</head>
<body>
<h3>Fetching XML-RPC Response</h3>
<?php
echo $bar->toHtml();
if (!$client->query('longwait.makemewait') ){
    $Errors = & IXR_Errors::instance();
    while ( $Error = $Errors->fetch() ) {
        echo ( $Error->code().': '.$Error->message().'<br>' );
    }
} else {
    echo ( '<h3>Response Complete...</h3>' );
    echo ( '<pre>' );
    print_r($client->getResponse());
    echo ( '</pre>' );
}

if ( $client->debug == TRUE ) {
    $Debug = & IXR_Debug::instance();
    echo ( '<pre>'.htmlentities($Debug->toString()).'</pre>' );
}
?>
</body>
</html>