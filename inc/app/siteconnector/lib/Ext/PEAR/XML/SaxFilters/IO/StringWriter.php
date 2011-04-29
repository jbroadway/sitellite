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
// $Id: StringWriter.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: StringWriter.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
if (!defined('XML_SAXFILTERS')) {
    define('XML_SAXFILTERS', 'XML/');
}
require_once(XML_SAXFILTERS.'SaxFilters/IO/StringReader.php');
/**
 * StringWriter writes data to a string
 * @access public
 * @package XML_SaxFilters
 */
class XML_SaxFilters_StringWriter extends Pear /* implements WriterInterface */
{
    /**
     * String to write to
     * @var string
     * @access private
     */
    var $string;

    /**
     * StringWriter Constructor
     * @access public
     */
    function XML_SaxFilters_StringWriter()
    {
        $this->string = '';
    }

    /**
     * Writes some data to the string
     * @param string data to write
     * @access public
     * @return mixed
     */
    function write($data)
    {
        $this->string.=$data;
        return true;
    }

    /**
     * Returns a StringReader
     * @param int buffer (optional)
     * @access public
     * @return object instance of StringReader
     */
    function & getReader($buffer=4096)
    {
        return new XML_SaxFilters_StringReader($this->string,$buffer);
    }

    /**
     * "Close" the string - does nothing
     * @access public
     * @return boolean
     */
    function close()
    {
        return true;
    }
}
?>