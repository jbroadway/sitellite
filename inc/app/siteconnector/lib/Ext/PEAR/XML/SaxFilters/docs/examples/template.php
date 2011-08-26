<?php
/*
This example shows how SaxFilters **might** be used to parse a template into
a PHP script that uses (imaginary) runtime components to render the page.
The output is crude and meant only to demonstrate the principle.
*/
# define('XML_SAXFILTERS', '../../');
define('XML_SAXFILTERS', 'XML/');
require_once(XML_SAXFILTERS.'XML_SaxFilters.php');
require_once(XML_SAXFILTERS.'SaxFilters/IO/FileWriter.php');
/**
 * HTMLfilter: main class for filtering HTML page templates
 * @access public
 */
class HTMLFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $registry=array();
    function HTMLFilter()
    {
        $this->registry['img']='HTMLImageFilter';
        $this->registry['list']='ListFilter';
    }
    /**
     * Start Element Handler
     * @param string name of tag
     * @param array tag attributes
     * @return void
     * @access protected
     */
    function startElementHandler($name,$attribs)
    {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( isset($attribs['runat']) && $attribs['runat']=='server' )
        {
            $name = trim($name);
            unset ( $attribs['runat'] );
            if ( array_key_exists($name,$this->registry) ) {
                if ( isset ( $attribs['id'] ) )
                    $reference = $attribs['id'];
                else
                    $reference = $name;
                $reference = trim($reference);
                $class = $this->registry[$name];
                $childFilter = & new $class($reference);
                $childFilter->setParent($this);
                $childFilter->setWriter($this->getWriter());
                $this->setChild($childFilter);
                $this->child->startElementHandler($name,$attribs);
            } else {
                die ( 'Unrecogized compiler component: '.$name );
            }
        }
        else
        {
            $this->writer->write("\$page->addHtml(\n");
            $this->writer->write('<'.$name);
            foreach ( $attribs as $name => $value )
            {
                if ( !empty($value) )
                {
                    $this->writer->write(' '.$name.'="'.$value.'"');
                }
                else
                {
                    $this->writer->write(' '.$name);
                }
            }
            $this->writer->write('>');
            $this->writer->write("\n);\n");
        }
    }

    /**
     * End Element Handler
     * @param string name of tag
     * @return void
     * @access protected
     */
    function endElementHandler($name)
    {
        if ( isset ( $this->child ) )
        {
            $this->child->endElementHandler($name);
        }
        else
        {
            $this->writer->write("\$page->addHtml(\n");
            $this->writer->write('</'.$name.'>');
            $this->writer->write("\n);\n");
        }
    }

    /**
     * Character Data Handler
     * @param string contents inside a tag
     * @return void
     * @access protected
     */
    function characterDataHandler($data)
    {
        $test = trim($data);
        if ( isset ( $this->child ) )
        {
            $this->child->characterDataHandler($data);
        }
        else if ( !empty($test) )
        {
            $this->writer->write("\$page->addHtml(\n");
            $this->writer->write($data);
            $this->writer->write("\n);\n");
        }
    }
}

/* Filter for images */
class HTMLImageFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $reference;
    function HTMLImageFilter ($reference)
    {
        $this->reference = $reference;
    }
    /**
     * Start Element Handler
     * @param string name of tag
     * @param array tag attributes
     * @return void
     * @access protected
     */
    function startElementHandler($name,$attribs)
    {
        $php="\$$this->reference = & new HTMLImage();\n";
        $php.= "\$attribs = array(";
        foreach ( $attribs as $name => $value ) {
            $value = trim($value);
            $php.="  '$name'=>'$value',\n";
        }
        $php.=");\n";
        $php.="\$$this->reference->setAttributes($attribs);\n";
        $php.="\$page->addComponent('$this->reference',\$$this->reference);\n";
        $this->writer->write($php);
        $this->detachFromParent();
    }
}

/* Filter for lists */
class ListFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $reference;
    var $listStart = false;
    var $inRow;
    function ListFilter ($reference)
    {
        $this->reference = $reference;
    }
    /**
     * Start Element Handler
     * @param string name of tag
     * @param array tag attributes
     * @return void
     * @access protected
     */
    function startElementHandler($name,$attribs)
    {
        if ( !$this->listStart ) {
            $php="\$$this->reference = & new List();\n";
            $php.="\$page->addComponent('$this->reference',\$$this->reference);\n";
            $this->writer->write($php);
            $this->listStart = true;
        }
        if ( $this->inRow )
        {
            if ( $name == 'item' )
            {
                $name = $attribs['id'];
                $php="\$$this->reference->setItem('$name');\n";
                $this->writer->write($php);
            }
        }
        else if ( $name == 'row' )
        {
            $this->inRow = true;
        }
        else
        {
            $this->writer->write("\$$this->reference->addHtml(\n");
            $this->writer->write('<'.$name);
            foreach ( $attribs as $name => $value )
            {
                if ( !empty($value) )
                {
                    $this->writer->write(' '.$name.'="'.$value.'"');
                }
                else
                {
                    $this->writer->write(' '.$name);
                }
            }
            $this->writer->write('>');
            $this->writer->write("\n);\n");
        }
    }
    /**
     * End Element Handler
     * @param string name of tag
     * @return void
     * @access protected
     */
    function endElementHandler($name)
    {
        if ( $name == 'list' )
        {
            $this->detachFromParent();
        }
        else if ( $name == 'row' )
        {
            $this->inRow = false;
        }
        else
        {
            $this->writer->write("\$$this->reference->addHtml(\n");
            $this->writer->write('</'.$name.'>');
            $this->writer->write("\n);\n");
        }
    }

    /**
     * Character Data Handler
     * @param string contents inside a tag
     * @return void
     * @access protected
     */
    function characterDataHandler($data)
    {
        $test = trim($data);
        if ( isset ( $this->child ) )
        {
            $this->child->characterDataHandler($data);
        }
        else if ( !empty($test) )
        {
            $this->writer->write("\$$this->reference->addHtml(\n");
            $this->writer->write($data);
            $this->writer->write("\n);\n");
        }
    }
}

// Create the parser
$file = 'page_template.tpl';
$parser = & XML_SaxFilters_createParser('HTMLSax','File',$file);

// Instantiate the FileWriter
$writer = & new XML_SaxFilters_FileWriter('compiled_template.php');

// Write some data
$writer->write("<?php\n");
$writer->write("\$page = new PageComponent();\n");

// Instantiate the RSSFilter
$filter = & new HTMLFilter();

// Set the writer that the filter will use
$filter->setWriter($writer);

// Set the parser's child to the filter
$parser->setChild($filter);

// Parse the XML document
$parser->parse();

// Finish writing
$writer->write("?>");
$writer->close();

// Display the file
highlight_file('compiled_template.php');
?>