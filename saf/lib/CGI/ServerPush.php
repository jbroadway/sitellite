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
// ServerPush is a class that implements Netscape's HTTP server push
// functionality, allowing incremental data to be presented to the
// visitor.
//

/**
	 * ServerPush is a class that implements Netscape's HTTP server push
	 * functionality, allowing incremental data to be presented to the
	 * visitor.
	 * 
	 * Please note that if you are using this class for progress meter-like
	 * purposes, for the sake of your bandwidth and for your users, do not
	 * send a message for each item being counted, instead wrap something like
	 * this around it:
	 * 
	 * if ($total > 1000 && $sent % floor ($total / 25) == 0) {
	 * 	$spush->send ('...', true);
	 * }
	 * 
	 * <code>
	 * <?php
	 * 
	 * $spush = new ServerPush ('replace');
	 * 
	 * // display the message "Hello, how are you today?" one word at a time
	 * // with a two second delay between words.
	 * $spush->rotate (array (
	 * 	'Hello,',
	 * 	'how',
	 * 	'are',
	 * 	'you',
	 * 	'today?',
	 * ), 2);
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CGI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-23, $Id: ServerPush.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class ServerPush {
	/**
	 * The unique key used to separate pushed data blocks.  This
	 * value is generated on the fly for you.
	 * 
	 * @access	public
	 * 
	 */
	var $key;

	/**
	 * The HTTP content type of the pushed document.  This can
	 * be either multipart/mixed or multipart/x-mixed-replace, depending
	 * on what you tell the constructor method.
	 * 
	 * @access	public
	 * 
	 */
	var $contentType;

	/**
	 * Constructor method.  $type can be either 'mixed', or 'replace'.
	 * 
	 * @access	public
	 * @param	string	$type
	 * 
	 */
	function ServerPush ($type = 'mixed') {
		if ($type == 'mixed') {
			$this->contentType = 'multipart/mixed';
		} else {
			$this->contentType = 'multipart/x-mixed-replace';
		}
		$this->key = md5 (time ());
		ob_implicit_flush ();
		header ('Content-type: ' . $this->contentType . '; boundary=' . $this->key);
	}

	/**
	 * Sends a single block to the visitor.  If $more is false,
	 * send() calls end() when it is done.  $contentType is the content
	 * type for this specific block.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @param	boolean	$more
	 * @param	string	$contentType
	 * 
	 */
	function send ($data, $more = true, $contentType = 'text/html') {
		echo "\n--" . $this->key . "\n";
		echo 'Content-type: ' . $contentType . "\n\n";
		echo $data . "\n";
		if (! $more) {
			$this->end ();
		}
	}

	/**
	 * Prints a closing key string, ending the transmission.
	 * 
	 * @access	public
	 * 
	 */
	function end () {
		echo "\n--" . $this->key . "--\n";
	}

	/**
	 * Sends a list of blocks with a delay of $sleep seconds
	 * between them.  This is the way web sites used to create basic
	 * animations back in the early days of the web, even before
	 * animated GIFs.  $data is an associative array where the keys
	 * are the content type of the data value.  If no content type
	 * is specified (ie. the array is not associative), then the
	 * content type will default to text/html.
	 * 
	 * @access	public
	 * @param	associative array	$data
	 * @param	integer	$sleep
	 * 
	 */
	function rotate ($data, $sleep = 1) {
		foreach ($data as $contentType => $d) {
			if (is_numeric ($contentType)) {
				$contentType = 'text/html';
			}
			$this->send ($d, true, $contentType);
			sleep ($sleep);
		}
		$this->end ();
	}
}



?>