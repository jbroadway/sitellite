<?php
// require PEAR::Validate
require_once('Validate.php');

# define('XML_SAXFILTERS', '../../../');
define('XML_SAXFILTERS', 'XML/');

require_once(XML_SAXFILTERS.'XML_SaxFilters.php');
require_once(XML_SAXFILTERS.'SaxFilters/FilterBuilder.php');
require_once(XML_SAXFILTERS.'SaxFilters/IO/StringWriter.php');

class FormFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    function FormFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('A','LinkFilter');
        $maps[] = new XML_SaxFilters_FilterMap('B','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('STRONG','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('I','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('EM','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('CODE','CodeFilter');
        $maps[] = new XML_SaxFilters_FilterMap('BLOCKQUOTE','TextBlock');
        $maps[] = new XML_SaxFilters_FilterMap('P','TextBlock');
        $maps[] = new XML_SaxFilters_FilterMap('BR','NewlineFilter');
        $maps[] = new XML_SaxFilters_FilterMap('OL','ListFilter');
        $maps[] = new XML_SaxFilters_FilterMap('UL','ListFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) )
        {
            $this->child->endElementHandler($name);
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
    /**
    * @static
    */
    function buildOpenTag($name,$attrs) {
        $openTag = '<'.$name;
        foreach ( $attribs as $key => $value ) {
            if ( $value !== true )
                $openTag .= ' '.$key.='"'.$value.'"';
            else
                $openTag .= ' '.$key;
        }
        return $openTag.='>';
    }

}

class LinkFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $opened = false;
    function LinkFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('B','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('STRONG','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('I','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('EM','EmFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        } else if ( !$this->openened && $name == 'A' ) {
            if ( isset($attribs['href']) && Validate::url($attribs['href']) ) {
                $tag = '<a href="'.$attribs['href'].' target="_blank">';
                $this->writer->write($tag);
            }
            $this->opened = true;
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'A' ) {
            $this->writer->write('</a>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
}

class StrongFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $opened = false;
    function StrongFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('A','LinkFilter');
        $maps[] = new XML_SaxFilters_FilterMap('I','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('EM','EmFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        } else if ( !$this->opened && ($name == 'B' || $name == 'STRONG') ) {
            $this->writer->write('<strong>');
            $this->opened = true;
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'B' || $name == 'STRONG' ) {
            $this->writer->write('</strong>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
}

class EmFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $opened = false;
    function EmFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('A','LinkFilter');
        $maps[] = new XML_SaxFilters_FilterMap('B','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('STRONG','StrongFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        } else if ( !$this->opened && ( $name == 'I' || $name == 'EM' ) ) {
            $this->writer->write('<em>');
            $this->opened = true;
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'I' || $name == 'EM' ) {
            $this->writer->write('</em>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
}

class CodeFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $opened = false;
    function startElementHandler($name,$attribs) {
        if ( !$this->opened && $name == 'CODE' ) {
            $this->writer->write('<code>');
            $this->opened = true;
        } else {
            $this->writer->write(FormFilter::buildOpenTag(strtolower($name),$attribs));
        }
    }
    function characterDataHandler($data) {
        $this->writer->write($data);
    }
    function endElementHandler($name) {
        if ( $name == 'CODE' ) {
            $this->writer->write('</code>');
            $this->parent->unsetChild();
        } else {
            $this->writer->write('</'.strtolower($name).'>');
        }
    }
}

class TextBlock extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $popened = false;
    var $bopened = false;
    function TextBlock()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('A','LinkFilter');
        $maps[] = new XML_SaxFilters_FilterMap('B','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('STRONG','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('I','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('EM','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('CODE','CodeFilter');
        $maps[] = new XML_SaxFilters_FilterMap('BR','NewlineFilter');
        $maps[] = new XML_SaxFilters_FilterMap('OL','ListFilter');
        $maps[] = new XML_SaxFilters_FilterMap('UL','ListFilter');
        $maps[] = new XML_SaxFilters_FilterMap('LI','ListItemFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        } else if ( !$this->popened && $name == 'P' ) {
            $this->writer->write('<p>');
            $this->popened = true;
        } else if ( !$this->bopened && $name == 'BLOCKQUOTE' ) {
            $this->writer->write('<blockquote>');
            $this->bopened = true;
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'P' ) {
            $this->writer->write('</p>');
            $this->parent->unsetChild();
        } else if ( $name == 'BLOCKQUOTE' ) {
            $this->writer->write('</blockquote>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
}

class NewLineFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    function startElementHandler($name,$attribs) {
        $this->writer->write('<br />');
        $this->parent->unsetChild();
    }
    function endElementHandler($name) {}
    function characterDataHandler($data) {}
}

class ListFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $uopened = false;
    var $oopened = false;
    function ListFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('LI','ListItemFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( !$this->uopened && $name == 'UL' )
        {
            $this->writer->write('<ul>');
        }
        else if ( !$this->oopened && $name == 'OL' )
        {
            $this->writer->write('<ol>');
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'UL' ) {
            $this->writer->write('</ul>');
            $this->parent->unsetChild();
        } else if ( $name == 'OL' ) {
            $this->writer->write('</ol>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
    }
}

class ListItemFilter extends XML_SaxFilters_AbstractFilter /* implements XML_SaxFilters_FilterInterface */
{
    var $filterBuilder;
    var $opened = false;
    function ListItemFilter()
    {
        $maps = array();
        $maps[] = new XML_SaxFilters_FilterMap('A','LinkFilter');
        $maps[] = new XML_SaxFilters_FilterMap('B','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('STRONG','StrongFilter');
        $maps[] = new XML_SaxFilters_FilterMap('I','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('EM','EmFilter');
        $maps[] = new XML_SaxFilters_FilterMap('OL','ListFilter');
        $maps[] = new XML_SaxFilters_FilterMap('UL','ListFilter');
        $this->filterBuilder = & new XML_SaxFilters_FilterBuilder($maps);
    }
    function startElementHandler($name,$attribs) {
        if ( isset ( $this->child ) )
        {
            $this->child->startElementHandler($name,$attribs);
        }
        else if ( $this->filterBuilder->makeFilter($this,$name) )
        {
            $this->child->setWriter($this->writer);
            $this->child->startElementHandler($name,$attribs);
        } else if ( !$this->opened && $name == 'LI' ) {
            $this->writer->write('<li>');
        }
    }
    function endElementHandler($name) {
        if ( isset ( $this->child ) ) {
            $this->child->endElementHandler($name);
        } else if ( $name == 'LI' ) {
            $this->writer->write('</li>');
            $this->parent->unsetChild();
        }
    }
    function characterDataHandler($data) {
        if ( isset($this->child) )
            $this->child->characterDataHandler($data);
        else
            $this->writer->write($data);
    }
}

function filterForm($text) {
    $parser = & XML_SaxFilters_createParser('HTMLSax','String',$text);
    $parser->parserSetOption('XML_OPTION_CASE_FOLDING',1);
    $filter = & new FormFilter();
    $writer = & new XML_SaxFilters_StringWriter();
    $filter->setWriter($writer);
    $parser->setChild($filter);
    $parser->parse();
    $reader = & $writer->getReader();
    $text = '';
    while ( ($slice = $reader->read()) !== false ) {
        $text.=$slice;
    }
    return $text;
}
?>