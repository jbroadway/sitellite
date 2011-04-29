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
// $Id: FilterBuilder.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
//
/**
* @package XML_SaxFilters
* @version $Id: FilterBuilder.php,v 1.1.1.1 2004/06/14 18:07:58 lux Exp $
*/
/**
* Required classes
*/
require_once('PEAR.php');
/**
* Class that, given a map of tag names to other filters, can be used
* to create filters.<br />
* Note: The API is experimental and may be subject to change.
* @access public
* @package XML_SaxFilters
*/
class XML_SaxFilters_FilterBuilder
{
    /**
     * Array of FilterMap objects
     * @var array
     * @access private
     */
    var $maps;
    /**
     * Constructs FilterBuilder
     * @param array
     * @access public
     */
    function XML_SaxFilters_FilterBuilder($maps)
    {
        $this->maps = $maps;
    }
    /**
     * Makes a filter an adds it as the parent filters child. Checks
     * to see if a filter should be built against the FilterMaps
     * @param object parent filter subclass of AbstractFilter
     * @param string XML opening tag element name
     * @param array XML opening tag attributes (optional)
     * @return boolean true on success
     * @access public
     */
    function makeFilter(& $parent,$name,$attrs=array())
    {
        foreach ( $this->maps as $map )
        {
            if ( $map->isFilter($name) && $map->trigger($attrs) )
            {
                $child = & new $map->filterName();
                $child->setParent($parent);
                $parent->setChild($child);
                return true;
            }
        }
        return false;
    }
    /**
     * Removes a filter from the parent, after checking the FilterMaps
     * @param object parent filter subclass of AbstractFilter
     * @param string XML closing tag element name
     * @return boolean true on success
     * @access public
     */
    function endFilter(& $parent,$name)
    {
        foreach ( $this->maps as $map )
        {
            if ( $map->isFilter($name) )
            {
                $parent->unsetChild();
                return true;
            }
        }
        return false;
    }
}
/**
* Defines a map which is used to identify when an Filter should be created.
* Used by XML_SaxFilters_FilterBuilder to create an remove filters.
* Note: The API is experimental and may be subject to change.
* @access public
* @package XML_SaxFilters
* @see XML_SaxFilters_FilterBuilder
*/
class XML_SaxFilters_FilterMap
{
    /**
     * XML Element name
     * @var string
     * @access private
     */
    var $tagName;
    /**
     * Name of Filter class to create (subclass of AbstractFilter
     * @var string
     * @access private
     */
    var $filterName;
    /**
     * Name of attribute to watch for
     * @var string
     * @access private
     */
    var $attrName;
    /**
     * Value of attribute
     * @var string
     * @access private
     */
    var $attrValue;
    /**
     * Constructs FilterMap
     * @param string XML element tag name
     * @param string Filter class to create
     * @param string attribute name (optional)
     * @param string attribute value (optional)
     * @access public
     */
    function XML_SaxFilters_FilterMap($tagName,$filterName,$attrName=null,$attrValue=null)
    {
        $this->tagName = strtolower($tagName);
        $this->filterName = $filterName;
        $this->attrName = $attrName;
        $this->attrValue = $attrValue;
    }
    /**
     * Checks to see a given XML element has a filter, using the tag name
     * @param string XML element tag name
     * @return boolean true if element has filter
     * @access protected
     */
    function isFilter($name) {
        if ( $this->tagName != strtolower($name) )
            return false;
        return true;
    }
    /**
     * Determines whether the XML element attributes are correct
     * to trigger creation of a filter.
     * @param array of XML element attributes
     * @return boolean true if element has filter
     * @access protected
     */
    function trigger($attrs)
    {
        if ( $this->attrName === null )
        {
            return true;
        }
        else
        {
            if ( array_key_exists($this->attrName,$attrs)
                && in_array($this->attrValue,$attrs) )
                return true;
            else
                return false;
        }

    }
}
?>