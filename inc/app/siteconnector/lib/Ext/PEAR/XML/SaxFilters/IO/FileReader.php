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
// $Id: FileReader.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: FileReader.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
/**
* FileReader streams data from a file for use by an XML parser
* @access public
* @package XML_SaxFilters
*/
class XML_SaxFilters_FileReader extends Pear /* implements ReaderInterface */
{
    /**
     * Name of source file: /path/filename
     * @var string
     * @access private
     */
    var $fileName;

    /**
     * Buffer size for file read
     * @var int
     * @access private
     */
    var $buffer;

    /**
     * PHP File resource
     * @var resource
     * @access private
     */
    var $fp = false;

    /**
     * FileReader Constructor
     * @param string path and name of file
     * @param int buffer size to read
     * @access public
     */
    function XML_SaxFilters_FileReader($fileName,$buffer=4096)
    {
        $this->fileName = $fileName;
        $this->buffer = $buffer;
    }

    /**
     * Returns some data from the source
     * @access public
     * @return mixed
     */
    function read()
    {
        if ( !$this->fp ) {
            if ( !$this->fp = fopen($this->fileName, 'r') )
                return $this->raiseError('Unable to open file: '.
                    $this->fileName);
        }
        if ( $data = fread($this->fp,$this->buffer) ) {
            return $data;
        } else {
            fclose($this->fp);
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
        return feof($this->fp);
    }
}
?>