<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the GNU General Public License (GPL) |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GPL Software License along    |
// | with this program; if not, write to Simian Systems, 242 Lindsay,     |
// | Winnipeg, MB, R3N 1H1, CANADA.  The License is also available at     |
// | the following web site address:                                      |
// | <http://www.sitellite.org/index/license>                             |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
// +----------------------------------------------------------------------+
//
// A class used to generate obscured email addresses, so as to make it
// more difficult for spambots to recognize them.
//

/**
	 * A class used to generate obscured email addresses, so as to make it
	 * more difficult for spambots to recognize them.
	 * 
	 * New in 1.2:
	 * - Moved from saf.EasyText.FakeEmail to saf.Misc.FakeEmail.
	 * - Removed the EasyText() and EasyTextInit() methods.
	 * 
	 * <code>
	 * <?php
	 * 
	 * echo FakeEmail::makeWords ('you@yoursite.com');
	 * echo FakeEmail::insertComments ('you@yoursite.com');
	 * echo FakeEmail::obfuscateLink ('you@yoursite.com');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Misc
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	1.2, 2003-05-23, $Id: FakeEmail.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class FakeEmail {
	/**
	 * Doesn't display the address as a link, but turns the various symbol
	 * characters into words and spaces the address out so that it reads properly
	 * aloud, but it doesn't look like an email address.
	 * 
	 * @access	public
	 * @param	string	$addy
	 * @return	string
	 * 
	 */
	function makeWords ($addy) {
		$addy = str_replace ('@', ' at ', $addy);
		$addy = str_replace ('.', ' dot ', $addy);
		$addy = str_replace ('-', ' dash ', $addy);
		$addy = str_replace ('_', ' underscore ', $addy);
		return $addy;
	}

	/**
	 * Doesn't display the address as a link, but inserts comments into
	 * it so as to make it difficult to read the address in the source code, but
	 * so that it still displays on the page as a normal address.
	 * 
	 * @access	public
	 * @param	string	$addy
	 * @return	string
	 * 
	 */
	function insertComments ($addy) {
		$addy = str_replace ('@', '<!-- foo bar -->@<!-- qwerty -->', $addy);
		$addy = str_replace (' at ', ' <!-- foo bar -->at<!-- qwerty --> ', $addy);
		$addy = str_replace ('.', '<!-- doo wop -->.<!-- shebop -->', $addy);
		return str_replace (' dot ', ' <!-- doo wop -->dot<!-- shebop --> ', $addy);
	}

	/**
	 * Still provides a mailto link, but obfuscates the email address
	 * inside the anchor link tag by converting various characters into alternate,
	 * but valid, encodings.
	 * 
	 * @access	public
	 * @param	string	$addy
	 * @return	string
	 * 
	 */
	function obfuscateLink ($addy) {
		$addy2 = str_replace ('@', '&#64;', $addy);
		$addy2 = str_replace ('a', '%61', $addy2);
		$addy2 = str_replace ('e', '%65', $addy2);
		$addy2 = str_replace ('i', '%69', $addy2);
		$addy2 = str_replace ('o', '%6F', $addy2);
		$addy2 = str_replace ('u', '%75', $addy2);
		$addy2 = str_replace ('y', '%79', $addy2);
		return '<a href="mailto:' . $addy2 . '">' . $addy . '</a>';
	}

	/**
	 * Provides all three obscuring methods in one, which means it will
	 * display as 'you at yoursite dot com', but will provide a mailto link as well.
	 * 
	 * @access	public
	 * @param	string	$addy
	 * @return	string
	 * 
	 */
	function doAll ($addy) {
		$addy2 = $addy;
		$addy = FakeEmail::makeWords ($addy);
		$addy = FakeEmail::insertComments ($addy);
		$linked = FakeEmail::obfuscateLink ($addy2);
		return str_replace ($addy2, $addy, $linked);
	}
}



?>