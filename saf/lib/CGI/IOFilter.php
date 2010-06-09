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
// IOFilter provides the skeleton for creating filters on incoming
// or outgoing data.
//

/**
	 * IOFilter provides the skeleton for creating filters on incoming
	 * or outgoing data, including the automatic sending of relevant HTTP
	 * headers.  Its purpose is to be extended through subclassing to create
	 * custom filters (ie. an XSLT filter).
	 * 
	 * <code>
	 * <?php
	 * 
	 * class XsltFilter extends IOFilter {
	 * 	var $xsltDoc = '';
	 * 
	 * 	function transform ($content) {
	 * 		if (empty ($this->xsltDoc)) {
	 * 			$this->error = 'No $xsltDoc property specified!';
	 * 			return false;
	 * 		}
	 * 
	 * 		global $loader;
	 * 		$loader->import ('saf.XML.XSLT');
	 * 		$xslt = new XSLT ();
	 * 
	 * 		if ($res = $xslt->process ($this->xsltDoc, $content)) {
	 * 			$xslt->free ();
	 * 			return $res;
	 * 		} else {
	 * 			$this->error = $xslt->error ();
	 * 			$xslt->free ();
	 * 			return false;
	 * 		}
	 * 	}
	 * }
	 * 
	 * $xslf = new XsltFilter ('xslt', 'text/html');
	 * 
	 * $xslf->xsltDoc = 'inc/xslt/xml2html.xslt';
	 * 
	 * if ($new_data = $xslf->transform ($original_data)) {
	 * 	$xslf->sendHeaders ();
	 * 	echo $new_data;
	 * } else {
	 * 	echo $xslf->error;
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	CGI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-10, $Id: IOFilter.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class IOFilter {
	/**
	 * A name for this filter.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The content type of the data, which if provided will be
	 * sent as an HTTP Content-Type header by sendHeaders().
	 * 
	 * @access	public
	 * 
	 */
	var $contentType;

	/**
	 * A list of extra HTTP headers to send by sendHeaders().
	 * 
	 * @access	public
	 * 
	 */
	var $extraHeaders;

	/**
	 * Contains an error message that may have been created during
	 * the execution of a transform() call.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$contentType
	 * 
	 */
	function IOFilter ($name, $contentType = false) {
		$this->name = $name;
		$this->contentType = $contentType;
		$this->extraHeaders = array ();
		$this->error = false;
	}

	/**
	 * Adds a header to $extraHeaders.
	 * 
	 * @access	public
	 * @param	string	$header
	 * 
	 */
	function addHeader ($header) {
		$this->extraHeaders[] = $header;
	}

	/**
	 * Transforms the content provided into a new format.  This
	 * method is left blank for subclasses to fill in.
	 * 
	 * @access	public
	 * @param	string	$content
	 * @return	string
	 * 
	 */
	function transform ($content) {
		// leave to subclasses to implement
		return $content;
	}

	/**
	 * Sends any HTTP headers specified, including the
	 * Content-Type header if $contentType is specified.
	 * 
	 * @access	public
	 * 
	 */
	function sendHeaders () {
		if ($this->contentType) {
			header ('Content-Type: ' . $this->contentType);
		}
		foreach ($this->extraHeaders as $header) {
			header ($header);
		}
	}
}



?>