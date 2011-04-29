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
// $Id: HTMLSaxParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* Implements PEAR::XML_HTMLSax for Sax filtering
* @package XML_SaxFilters
* @version $Id: HTMLSaxParser.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
if (!defined('XML_SAXFILTERS')) {
    define('XML_SAXFILTERS', 'XML/');
}
if (!defined('XML_HTMLSAX')) {
    define('XML_HTMLSAX', 'XML/');
}
require_once(XML_HTMLSAX.'XML_HTMLSax.php');
require_once(XML_SAXFILTERS.'SaxFilters/AbstractParser.php');
/**
 * HtmlSaxParser adapts XML_HTMLSax parser to allow filtering
 * on badly formed XML
 * @access public
 * @package XML_SaxFilters
 */
class XML_SaxFilters_HtmlSaxParser extends XML_SaxFilters_AbstractParser /* implements XML_SaxFilters_ParserInterface */
{
    /**
     * Stores an instance of the parser
     *
     * @var object
     * @access private
     */
    var $parser;

    /**
     * Constructs HtmlSaxParser
     * @param object class implementing ReaderInterface
     * @access public
     */
    function XML_SaxFilters_HtmlSaxParser(& $reader)
    {
        parent::XML_SaxFilters_AbstractParser($reader);
        $this->parser=& new XML_HTMLSax();
        $this->parser->set_object($this);
        $this->parser->set_element_handler('startElementHandler','endElementHandler');
        $this->parser->set_data_handler('characterDataHandler');
    }

    /**
     * Sets a Sax parser option
     * @param string option name
     * @param string option value
     * @return boolean
     * @access public
     */
    function parserSetOption($opt,$val) {
        return $this->parser->set_option($opt, $val);
    }

    /**
     * Parse the XML stream
     * @return void
     * @access public
     */
    function parse() {
        while ( $data = $this->reader->read() ) {
            $this->parser->parse($data);
        }
        return true;
    }
}
?>