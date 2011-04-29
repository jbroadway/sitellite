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
// $Id: ExpatParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: ExpatParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
if (!defined('XML_SAXFILTERS')) {
    define('XML_SAXFILTERS', 'XML/');
}
require_once(XML_SAXFILTERS.'SaxFilters/AbstractParser.php');
/**
* ExpatParser for the native PHP SAX XML extension
* @access public
* @package XML_SaxFilters
*/
class XML_SaxFilters_ExpatParser extends XML_SaxFilters_AbstractParser /* implements XML_SaxFilters_ParserInterface */
{
    /**
     * Stores an instance of the parser
     *
     * @var resource
     * @access private
     */
    var $parser;

    /**
     * Constructs ExpatParser
     * Note: modify the buffer for large documents
     * @param object XMLReader
     * @access public
     */
    function XML_SaxFilters_ExpatParser(& $reader)
    {
        parent::XML_SaxFilters_AbstractParser($reader);
        $this->parser=xml_parser_create();
        xml_set_object($this->parser,$this);
        xml_set_element_handler($this->parser,'startElementHandler','endElementHandler');
        xml_set_character_data_handler($this->parser,'characterDataHandler');
        xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
    }

    /**
     * Sets a Sax parser option
     * @param string option name
     * @param string option value
     * @access public
     */
    function parserSetOption($opt,$val)
    {
        return xml_parser_set_option ( $this->parser, $opt, $val);
    }

    /**
     * Parse the XML stream
     * @return mixed PEAR Error or true
     * @access public
     */
    function parse()
    {
        while ($data = $this->reader->read()){
            if (!xml_parse($this->parser, $data, $this->reader->isFinal())) {
                $errorString=xml_error_string(xml_get_error_code($this->parser));
                $line=xml_get_current_line_number($this->parser);
                return $this->raiseError('Parser error: '.$errorString.' on line '.
                    $line.' in XML document');
            }
        }
        xml_parser_free($this->parser);
        return true;
    }
}
?>