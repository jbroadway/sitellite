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
// $Id: ParserInterface.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: ParserInterface.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* ParserInterface defines methods which SaxParsers must implement
* @package XML_SaxFilters
*/
class XML_SaxFilters_ParserInterface
{
    /**
     * Sets a Sax parser option
     * @param string option name
     * @param string option value
     * @access public
     * @return mixed
     */
    function parserSetOption($opt,$val) {}

    /**
     * Tells the SAX parser to parse
     * @return mixed PEAR Error or true
     */
    function parse() {}
}
?>