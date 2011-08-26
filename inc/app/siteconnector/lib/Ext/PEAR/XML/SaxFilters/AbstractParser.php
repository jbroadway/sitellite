<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Luis Argerich <lrargerich@yahoo.com> Original Author        |
// | Authors: Harry Fuecks <hfuecks@phppatterns.com> Port to PEAR + more  |
// +----------------------------------------------------------------------+
//
// $Id: AbstractParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: AbstractParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
/**
* Base class for Sax Parsers to extend.
* @access public
* @abstract
* @package XML_SaxFilters
*/
class XML_SaxFilters_AbstractParser extends Pear
{
    /**
     * Stores the reader
     *
     * @var object
     * @access private
     */
    var $reader;

    /**
     * Stores the child filter
     *
     * @var object
     * @access private
     */
    var $child;

    /**
     * Constructs AbstractParser
     * @param object class implementing ReaderInterface
     * @access public
     * @abstract
     */
    function XML_SaxFilters_AbstractParser(& $reader)
    {
        $this->reader = & $reader;
    }

    /**
     * Sets the child
     * @param object class or subclass of AbstractFilter
     * @return void
     */
    function setChild(& $child)
    {
        $this->child=& $child;
    }

    /**
     * Unsets the child
     * @return void
     */
    function unsetChild()
    {
        unset($this->child);
    }

    /**
     * Sax start element handler
     * @param object instance of the parser
     * @param string element name
     * @param array element attributes
     * @return void
     * @access protected
     */
    function startElementHandler($parser,$name,$attribs)
    {
        $this->child->startElementHandler($name,$attribs);
    }

    /**
     * Sax end element handler
     * @param object instance of the parser
     * @param string element name
     * @return void
     * @access protected
     */
    function endElementHandler($parser,$name)
    {
        $this->child->endElementHandler($name);
    }

    /**
     * Sax character data handler
     * @param object instance of the parser
     * @param string contents of element
     * @return void
     * @access protected
     */
    function characterDataHandler($parser,$data)
    {
        $this->child->characterDataHandler($data);
    }
}
?>