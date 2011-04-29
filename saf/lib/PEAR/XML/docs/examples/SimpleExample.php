<?php
/***
 * $Id: SimpleExample.php,v 1.1.1.1 2005/04/29 04:44:43 lux Exp $
 * Shows all the handlers in use with a simple document
 */
require_once('XML/XML_HTMLSax.php');


class MyHandler {
    function MyHandler(){}
    function openHandler(& $parser,$name,$attrs) {
        echo ( 'Open Tag Handler: '.$name.'<br />' );
        echo ( 'Attrs:<pre>' );
        print_r($attrs);
        echo ( '</pre>' );
    }
    function closeHandler(& $parser,$name) {
        echo ( 'Close Tag Handler: '.$name.'<br />' );
    }
    function dataHandler(& $parser,$data) {
        echo ( 'Data Handler: '.$data.'<br />' );
    }
    function escapeHandler(& $parser,$data) {
        echo ( 'Escape Handler: '.$data.'<br />' );
    }
    function piHandler(& $parser,$target,$data) {
        echo ( 'PI Handler: '.$target.' - '.$data.'<br />' );
    }
    function jaspHandler(& $parser,$data) {
        echo ( 'Jasp Handler: '.$data.'<br />' );
    }
}

$doc=<<<EOD
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>HTML Sax in Action</title>
<meta name="Description" content="Example for HTML Sax">
<!-- Some JavaScript inside a CDATA block coming right up... -->
<script type="application/x-javascript">
<![CDATA[
document.write('<b>Hello World!</b>');
]]>
</script>
</head>
<body>
<?php
echo ( '<b>This is a processing instruction</b>' );
?>
<a href="http://www.php.net">PHP</a>
<%
document.write('<i>Hello World!</i>');
%>
</body>
</html>
EOD;

// Instantiate the handler
$handler=new MyHandler();

// Instantiate the parser
$parser=& new XML_HTMLSax();

// Register the handler with the parser
$parser->set_object($handler);

// Set a parser option
$parser->set_option('XML_OPTION_TRIM_DATA_NODES');

// Set the handlers
$parser->set_element_handler('openHandler','closeHandler');
$parser->set_data_handler('dataHandler');
$parser->set_escape_handler('escapeHandler');
$parser->set_pi_handler('piHandler');
$parser->set_jasp_handler('jaspHandler');

// Parse the document
$parser->parse($doc);
?>