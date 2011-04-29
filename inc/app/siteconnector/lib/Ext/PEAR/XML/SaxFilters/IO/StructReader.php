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
// $Id: StructReader.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: StructReader.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
/**
 * StructReader iterates over a struct. This isn't
 * meant for use directly by the filters but simply
 * to be returned by StructWriter
 * @access public
 * @package XML_SaxFilters
 */
class XML_SaxFilters_StructReader extends Pear /* implements ReaderInterface */
{
    /**
     * Struct to read from
     * @var array
     * @access private
     */
    var $struct;

    /**
     * Keeps track of the array pointer
     * @var int
     * @access private
     */
    var $pointer;

    /**
     * StringReader Constructor
     * @param array struct to read from
     * @access public
     */
    function XML_SaxFilters_StructReader(& $struct)
    {
        $this->struct = & $struct;
        $this->pointer = -1;
    }

    /**
     * Returns an object from the struct
     * @access public
     * @return object
     */
    function read()
    {
        $struct = each ( $this->struct );
        if ( is_array($struct) ) {
            $this->pointer ++;
            return $struct['value'];
        } else {
            return false;
        }
    }

    /**
     * Indicates whether the reader has reached the end of the data source
     * @access public
     * @return boolean
     */
    function isFinal()
    {
        $size = count($this->struct) - 1;
        if ( $this->pointer >= $size )
            return true;
        else
            return false;
    }
}
?>