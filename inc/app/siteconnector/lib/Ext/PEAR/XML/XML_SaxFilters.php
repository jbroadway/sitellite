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
// $Id: XML_SaxFilters.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: XML_SaxFilters.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Includes
*/
require_once('PEAR.php');
if (!defined('XML_SAXFILTERS')) {
    define('XML_SAXFILTERS', 'XML/');
}
require_once(XML_SAXFILTERS.'SaxFilters/AbstractFilter.php');
/**
* Sets a Sax parser option
* @param string parser type ('Expat' or 'HTMLSax')
* @param string reader type ('File','String' or 'Struct')
* @access public
* @return mixed
* @package XML_SaxFilters
*/
function &XML_SaxFilters_createParser($parserType,$readerType,& $input)
{
    switch ( strtolower($readerType) ) {
        case 'file':
            require_once(XML_SAXFILTERS.'/SaxFilters/IO/FileReader.php');
            $reader = & new XML_SaxFilters_FileReader($input);
        break;
        case 'string':
            require_once(XML_SAXFILTERS.'/SaxFilters/IO/StringReader.php');
            $reader = & new XML_SaxFilters_StringReader($input);
        break;
        case 'struct':
            require_once(XML_SAXFILTERS.'/SaxFilters/IO/StructReader.php');
            $reader = & new XML_SaxFilters_StructReader($input);
        break;
        default:
            return PEAR::raiseError('Unrecognized reader type: '.$readerType);
        break;
    }
    switch ( strtolower($parserType) ) {
        case 'expat':
            require_once(XML_SAXFILTERS.'/SaxFilters/ExpatParser.php');
            $parser = & new XML_SaxFilters_ExpatParser($reader);
        break;
        case 'htmlsax':
            require_once(XML_SAXFILTERS.'/SaxFilters/HTMLSaxParser.php');
            $parser = & new XML_SaxFilters_HTMLSaxParser($reader);
        break;
        default:
            return PEAR::raiseError('Unrecognized parser type: '.$parserType);
        break;
    }
    return $parser;
}
?>