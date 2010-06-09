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
// RSS uses XSLT and Cache to generate content syndicated from other web
// sites and to cache it so as not to put unnecessarily load on those
// sites.
//

$GLOBALS['loader']->import ('saf.Cache');
$GLOBALS['loader']->import ('saf.XML.XSLT');

/**
	 * RSS uses XSLT and Cache to generate content syndicated from other web
	 * sites and to cache it so as not to put unnecessarily load on those
	 * sites.  RSS also exposes an EasyText method, which adds support for the
	 * EasyText =RSS tag.  Please note: this class depends on the Cache and
	 * XSLT classes.
	 * 
	 * New in 1.2:
	 * - Added the ability to create RSS documents instead of simply processing them.
	 *   This is compatible with RSS version 0.91, but I don't know about 1.0.
	 * 
	 * New in 1.4:
	 * - Fixed a bug in the EasyText method (stupid spelling mistake!).  Thanks Oleg
	 *   for the report!
	 * 
	 * New in 1.6:
	 * - Removed the EasyText() and EasyTextInit() methods.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $rss = new RSS;
	 * 
	 * $url = 'http://somesite.com/content.rss';
	 * $rss_content = $rss->getUrl ($url);
	 * $stylesheet = $rss->getXsl ('inc/xsl/somesite.xsl');
	 * 
	 * $data = $rss->process ($stylesheet, $rss_content, 'cache', 900, $url);
	 * if ($data) {
	 * 	echo $data;
	 * } else {
	 * 	echo $rss->xslt->error;
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.6, 2003-05-23, $Id: RSS.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class RSS {
	/**
	 * This is an XSLT processor resource returned by the
	 * xslt_create () function.  Please note: the XSLT processor
	 * is not created until the process method of this class is
	 * called, so as to maintain compatibility with systems that
	 * do not have Sablotron support installed, but are using
	 * EasyText.
	 * 
	 * @access	public
	 * 
	 */
	var $xslt;

	/**
	 * If an RSS document is being created, this will
	 * contain the XMLDoc object.
	 * 
	 * @access	public
	 * 
	 */
	var $doc;

	/**
	 * If an RSS document is being created, this will
	 * contain a reference to the root node.
	 * 
	 * @access	public
	 * 
	 */
	var $docroot;

	/**
	 * If an RSS document is being created, this will
	 * contain a reference to the current channel.
	 * 
	 * @access	public
	 * 
	 */
	var $channel;

	/**
	 * Returns the contents of the specified url.
	 * 
	 * @access	public
	 * @param	string	$url
	 * @return	string
	 * 
	 */
	function getUrl ($url) {
		return join ('', file ($url));
	}

	/**
	 * Returns the contents of the specified xsl file.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 * 
	 */
	function getXsl ($file) {
		return join ('', file ($file));
	}

	/**
	 * Returns the contents of the transformed data, or
	 * its cached equivalent.  Returning 0 means there was an XSL
	 * transformation error.
	 * 
	 * @access	public
	 * @param	string	$xsldata
	 * @param	string	$xmldata
	 * @param	string	$cache_dir
	 * @param	string	$cache_duration
	 * @param	string	$cache_file
	 * @return	string
	 * 
	 */
	function process ($xsldata, $xmldata, $cache_dir, $cache_duration, $cache_file) {
		$this->xslt = new XSLT;
		$cache = new Cache ($cache_dir);

		if ($cache->expired ('RSS_' . $cache_file, $cache_duration)) {
			//echo 're-caching';
			// re-cache the file
			ob_start ();

			// go about our business

			if ($processed = $this->xslt->process ($xsldata, $xmldata)) {
				echo $processed;
			} else {
				echo 0;
			}

			// grab the output and cache it
			$data = ob_get_contents ();
			ob_end_clean ();
			$cache->file ('RSS_' . $cache_file, $data);
			return $data;

		} else {
			// show the cached version
			//echo 'cached';
			return $cache->show ('RSS_' . $cache_file);
		}
	}

	/**
	 * Creates a new XMLDoc object, sets the root node to 'rss',
	 * and also defines its doctype.  Note: $version is the RSS version,
	 * not the XML version.
	 * 
	 * @access	public
	 * @param	string	$version
	 * 
	 */
	function makeDoc ($version = '0.91') {
		global $loader;
		$loader->import ('saf.XML.Doc');
		$this->doc = new XMLDoc ();
		$this->docroot =& $this->doc->addRoot ('rss');
		$this->docroot->setAttribute ('version', $version);
		$this->doc->doctype = '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "http://my.netscape.com/publish/formats/rss-0.91.dtd">';
	}

	/**
	 * Allows you to easily modify the doctype of the current
	 * RSS document being created.
	 * 
	 * @access	public
	 * @param	string	$doctype
	 * 
	 */
	function setDoctype ($doctype) {
		$this->doc->doctype = $doctype;
	}

	/**
	 * Creates a new channel XMLNode.
	 * 
	 * @access	public
	 * @param	string	$title
	 * @param	string	$link
	 * @param	string	$description
	 * @param	string	$language
	 * 
	 */
	function addChannel ($title, $link, $description = '', $language = 'en-us') {
		$this->channel =& $this->docroot->addChild ('channel');
		$this->channel->addChild ('title', $title);
		$this->channel->addChild ('link', $link);
		if (! empty ($description)) {
			$this->channel->addChild ('description', $description);
		}
		if (! empty ($language)) {
			$this->channel->addChild ('language', $language);
		}
	}

	/**
	 * Creates a new item XMLNode and adds it to the currently
	 * active channel.
	 * 
	 * @access	public
	 * @param	string	$title
	 * @param	string	$link
	 * @param	string	$description
	 * 
	 */
	function addItem ($title, $link, $description = '') {
		$item =& $this->channel->addChild ('item');
		$item->addChild ('title', $title);
		$item->addChild ('link', $link);
		if (! empty ($description)) {
			$item->addChild ('description', $description);
		}
	}

	/**
	 * Calls the write() method on the current RSS document,
	 * and returns the result.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function write () {
		return $this->doc->write ();
	}
}



?>