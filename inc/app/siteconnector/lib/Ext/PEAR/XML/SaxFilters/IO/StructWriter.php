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
// $Id: StructWriter.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* Required classes
*/
require_once('PEAR.php');
if (!defined('XML_SAXFILTERS')) {
    define('XML_SAXFILTERS', 'XML/');
}
require_once(XML_SAXFILTERS.'SaxFilters/IO/StructReader.php');
/**
 * StructWriter writes objects to an array
 * @access public
 * @package XML_SaxFilters
 */
class XML_SaxFilters_StructWriter extends Pear /* implements WriterInterface */
{
    /**
     * Struct to write to
     * @var array
     * @access private
     */
    var $struct;

    /**
     * StructWriter Constructor
     * @access public
     */
    function XML_SaxFilters_StructWriter()
    {
        $this->struct = array();
    }

    /**
     * Adds an object to the struct
     * @param object data to write
     * @access public
     * @return mixed
     */
    function write($data)
    {
        $this->struct[]=$data;
        return true;
    }

    /**
     * Returns a StructReader
     * @access public
     * @return object instance of StringReader
     */
    function & getReader()
    {
        return new XML_SaxFilters_StructReader($this->struct);
    }

    /**
     * "Close" the struct - does nothing
     * @access public
     * @return boolean
     */
    function close()
    {
        return true;
    }
}
?>