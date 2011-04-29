<?php
# define('XML_SAXFILTERS', '../../');
define('XML_SAXFILTERS', 'XML/');

// Include AbstractFilter and ParserFactory
require_once(XML_SAXFILTERS.'XML_SaxFilters.php');

// Define a customer handler class
class MyFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    // Parsed output stored here
    var $output;

    function MyFilter(){
        $this->output='';
    }

    function startElementHandler($name,$attribs)
    {
        $this->output.="Open Tag Name: ".$name."\n";
        if ( count($attribs) > 0 )
        {
            foreach ( $attribs as $key => $value )
            {
                $this->output.="    $key: $value\n";
            }
        }
    }

    function endElementHandler($name)
    {
        $this->output.="Close Tag Name: ".$name."\n";
    }

    function characterDataHandler($data)
    {
        $data = trim($data);
        if ( !empty($data) )
            $this->output.="Data:\n ".$data."\n";
    }
}

// A Simple XML document
$doc = <<<EOD
<?xml version="1.0"?>
<dynamically_typed_languages>
    <language name="PHP" version="4.3.2">
        PHP is number 1 for building web based applications.
        <url>http://www.php.net</url>
    </language>
    <language name="Python" version="2.2.3">
        Python is number 1 for cross platform desktop applications.
        <url>http://www.php.net</url>
    </language>
    <language name="Perl" version="5.8.0">
        Perl is number 1 for building backend server applications.
        <url>http://www.perl.org</url>
    </language>
</dynamically_typed_languages>
EOD;

// Create the parser
$parser = & XML_SaxFilters_createParser('Expat','String',$doc);

// Instantiate the filter
$filter = & new MyFilter();

// Add the filter to the parser
$parser->setChild($filter);

// Parse
$test = $parser->parse();
if ( PEAR::isError($test) ) {
    echo ( '<pre>' );
    print_r($test);
    echo ( '</pre>' );
}

echo ( '<pre>' );
echo ( $filter->output );
echo ( '</pre>' );
?>