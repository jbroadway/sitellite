<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// XSLT is a very minimalistic wrapper around PHP's Sablotron functions.
// The benefits in a class such as this are that a) it's Object Oriented,
// and b) the Sablotron functions have a big "WARNING: EXPERIMENTAL" in
// the documentation, so if one was to code an application using these
// method calls as opposed to hard-coding the PHP functions, when the
// names change, you simply have to upgrade the class and not your code.
//

/**
 * @package XML
 */
class XSLT {
	/**
	 * This is the XSLT processor resource returned by the
	 * xslt_create () function.
	 * 
	 * @access	public
	 * 
	 */
	var $handle;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function XSLT () {
		$this->handle = @xslt_create ();
	}

	/**
	 * Transforms the given XML data and XSL stylesheet and
	 * returns the completed transformation as a string, or returns
	 * zero (0) in case of failure.  Note: The $params property only
	 * works with more recent versions of PHP (ie. 4.2+).
	 * 
	 * @access	public
	 * @param	string	$xsl_data
	 * @param	string	$xml_data
	 * @param	associative array	$params
	 * @return	string
	 * 
	 */
	function process ($xsl_data = '', $xml_data = '', $params = array ()) {
		if (PHP_VERSION < '4.1.0') {
			if (xslt_process ($xsl_data, $xml_data, $res)) {
				return $res;
			} else {
				return 0;
			}
		} else {
			$args = array ();
			if (@file_exists ($xsl_data)) {
				$xsltfile = $xsl_data;
			} else {
				$xsltfile = 'arg:/_xsl';
				$args['/_xsl'] = $xsl_data;
			}
			if (@file_exists ($xml_data)) {
				$xmlfile = $xml_data;
			} else {
				$xmlfile = 'arg:/_xml';
				$args['/_xml'] = $xml_data;
			}
			$res = xslt_process ($this->handle, $xmlfile, $xsltfile, null, $args, $params);
			if ($res) {
				return $res;
			} else {
				return 0;
			}
		}
	}

	/**
	 * Returns the current error message.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function error () {
		if (PHP_VERSION < '4.1.0') {
			return @xslt_error ();
		} else {
			return @xslt_error ($this->handle);
		}
	}

	/**
	 * Returns the current error number.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function errno () {
		if (PHP_VERSION < '4.1.0') {
			return @xslt_errno ();
		} else {
			return @xslt_errno ($this->handle);
		}
	}

	/**
	 * Frees the XSL processor.
	 * 
	 * @access	public
	 * 
	 */
	function free () {
		@xslt_free ($this->handle);
		unset ($this->handle);
	}
}



?>