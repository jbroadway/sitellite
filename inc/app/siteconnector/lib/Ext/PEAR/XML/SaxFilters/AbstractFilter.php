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
// $Id: AbstractFilter.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: AbstractFilter.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
/**
* Base class for Sax Filters to extend.
* Provides methods for dealing with listeners, parents and writers.
* @access public
* @abstract
* @package XML_SaxFilters
*/
class XML_SaxFilters_AbstractFilter extends Pear
{
    /**
     * Stores the listener
     *
     * @var object class or subclass of AbstractFilter
     * @access private
     */
    var $child;

    /**
     * Stores the parent listener is filtering recursively
     *
     * @var object class or subclass of AbstractFilter
     * @access private
     */
    var $parent;

    /**
     * Stores the writer object
     *
     * @var object class implementing WriterInterface
     * @access private
     */
    var $writer;

    /**
     * Sets the child filter to which events are delegated
     * @param object class or subclass of AbstractFilter
     * @return void
     */
    function setChild(& $child)
    {
        $this->child=& $child;
    }

    /**
     * Unsets the child filter
     * @return void
     */
    function unsetChild()
    {
        unset($this->child);
    }

    /**
     * Sets the parent filter allow the child to talk back
     * @param object class or subclass of AbstractFilter
     * @return void
     */
    function setParent(& $parent)
    {
        $this->parent=& $parent;
    }

    /**
     * Breaks the connection to from the child to the parent filter.
     * @return void
     */
    function unsetParent()
    {
        unset($this->parent);
    }

    /**
     * Calls the parent setChild() method allow a child filter
     * to set another child filter in the parent
     * @param object class or subclass of AbstractFilter
     * @return void
     */
    function attachToParent (& $child)
    {
        $this->parent->setChild($child);
    }

    /**
     * Calls the parent unsetChild() method removing any child filter
     * from the parent
     * @return void
     */
    function detachFromParent ()
    {
        $this->parent->unsetChild();
    }

    /**
     * Sets the writer
     * @param object class implementing WriterInterface
     * @return void
     */
    function setWriter(& $writer)
    {
        $this->writer=& $writer;
    }

    /**
     * Unsets the writer
     * @return object class implementing WriterInterface
     */
    function & getWriter()
    {
        return $this->writer;
    }
}
?>