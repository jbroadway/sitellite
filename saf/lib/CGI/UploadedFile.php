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
// This class add to the CGI class by providing a simplified means of
// dealing with uploaded files.
//

/**
	 * This class add to the CGI class by providing a simplified means of
	 * dealing with uploaded files.
	 * 
	 * New in 1.4:
	 * - Fixed a bug in the move() method.
	 * 
	 * New in 1.6:
	 * - Added the get() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $file = new UploadedFile ($HTTP_POST_FILES['filename']);
	 * 
	 * $file->move ('/new/path/to', 'file');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CGI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.6, 2003-01-24, $Id: UploadedFile.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class UploadedFile {
	/**
	 * The name of the uploaded file.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The mime type of the uploaded file.
	 * 
	 * @access	public
	 * 
	 */
	var $type;

	/**
	 * The size of the uploaded file.
	 * 
	 * @access	public
	 * 
	 */
	var $size;

	/**
	 * The full path and name to the temporary upload file.
	 * 
	 * @access	public
	 * 
	 */
	var $tmp_name;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function UploadedFile ($hash) {
		$this->name = $hash['name'];
		$this->type = $hash['type'];
		$this->size = $hash['size'];
		$this->tmp_name = $hash['tmp_name'];
	}

	/**
	 * Moves the file to the specified location.  The $to_file
	 * parameter is optional.  You can include the name of the file
	 * in the first paraemeter, or use the second, but if so, make sure
	 * the $to_path parameter does not end with a trailing slash (/).
	 * 
	 * @access	public
	 * @param	string	$to_path
	 * @param	string	$to_file
	 * @return	boolean
	 * 
	 */
	function move ($to_path, $to_file = '') {
		if (! empty ($to_file)) {
			$to_file = '/' . $to_file;
		} else {
			$to_file = '/' . $this->name;
		}
		return move_uploaded_file ($this->tmp_name, $to_path . $to_file);
	}

	/**
	 * Gets the contents of the file as one big fat string, so you
	 * can do with them as you please.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function get () {
		return @join ('', @file ($this->tmp_name));
	}
}



?>