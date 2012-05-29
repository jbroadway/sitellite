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
// Password widget.  Displays an HTML <input type="password" /> form field.
//

/**
	 * Password widget.  Displays an HTML <input type="password" /> form field.
	 * 
	 * New in 1.2:
	 * - Added encrypt() and verify() methods.
	 * 
	 * New in 1.4:
	 * - Added a makeStrong() method that automatically defines a series of recommended
	 *   rules for all passwords, such as minimum length and required characters to
	 *   reduce the odds of guessability.
	 * - Added a generate() method that generates strong passwords for you.
	 * - Added a $ignoreEmpty property.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_password ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-08-27, $Id: Password.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_password extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'password';

	/**
	 * Determines whether or not this password field should be ignored
	 * if left blank.  This is useful for situations where a password change
	 * field may be present in a form but is not required to be filled out to
	 * change it since the current value cannot or should not be sent
	 * back to the browser.  Defaults to true.
	 * 
	 * @access	public
	 * 
	 */
	var $ignoreEmpty = true;

	/**
	 * Semias Alex 29-5-2012
	 * Password helper -> strength indicator & generator
	 * verifyField is optional. If it's given the password generator
	 * will also copy the password to that field.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordHelp = false;
	
	
	//minLength is only used when turnOnHelp() or makeStrong() is used
	var $minLength = 8;
	var $verifyFieldId = '';
	
	function turnOnHelp ($verifyFieldId = '') {
		global $intl;
		$this->minLength = security_min_pass_length();
		$this->addRule ('length "'.$this->minLength.'+"', $intl->get ('Your password must be at least '.$this->minLength.' characters in length.'));
		$this->passwordHelp = true;
		$this->verifyFieldId = $verifyFieldId;
	}
	

	/**
	 * Encrypts the value given with the optional salt.  If the
	 * value is also missing, uses the $data_value property.  Returns the
	 * encrypted string.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	string	$salt
	 * @return	string
	 * 
	 */
	function encrypt ($value = '', $salt = '') {
		return better_crypt ($value, $salt);
	}

	/**
	 * Verifies input agains an encrypted value to see if it
	 * matches.
	 * 
	 * @access	public
	 * @param	string	$input
	 * @param	string	$encrypted
	 * @return	boolean
	 * 
	 */
	function verify ($input, $encrypted) {
		return better_crypt_compare ($input, $encrypted);
	}

	/**
	 * Creates a series of rules for the current widget that state the
	 * minimum length of a valid password (8), and that it must contain at least
	 * one of each of the following: a lowercase letter, an uppercase letter,
	 * a number, a symbol.
	 * 
	 * @access	public
	 * 
	 */
	function makeStrong () {
		global $intl;
		$this->minLength = security_min_pass_length();
		$this->addRule ('length "'.$this->minLength.'+"', $intl->get ('Your password must be at least '.$this->minLength.' characters in length.'));
		$this->addRule ('regex "[a-z]"', $intl->get ('Your password must contain at least one lowercase letter.'));
		$this->addRule ('regex "[A-Z]"', $intl->get ('Your password must contain at least one uppercase letter.'));
		$this->addRule ('regex "[0-9]"', $intl->get ('Your password must contain at least one number.'));
		$this->addRule ('regex "[^a-zA-Z0-9]"', $intl->get ('Your password must contain at least one symbol.'));
	}

	/**
	 * Creates a "secure" password of the specified $length using a random
	 * combination of lowercase and uppercase letters, numbers, and symbols
	 * (containing one of each for every four characters of the password, not
	 * necessarily in any order).  This aims to help site administrators enforce
	 * more secure password policies, in conjunction with the makeStrong() method
	 * for verifying that user-created passwords meet certain security criteria.
	 * 
	 * @access	public
	 * @param	integer	$length
	 * @return	string
	 * 
	 */
	function generate ($length = 8) {
		$clist = array ();
		$clist[] = 'abcdefghijklmnopqrstuvwxyz';
		$clist[] = strtoupper ($clist[0]);
		$clist[] = '1234567890';
		$clist[] = '~`!@#$%^&*()+=.,/?\\|{}[]<>\'"_-';
		$orders = array (
			'1234', '1243', '1324', '1342', '1423', '1432',
			'2134', '2143', '2314', '2341', '2413', '2431',
			'3124', '3142', '3214', '3241', '3412', '3421',
			'4123', '4132', '4213', '4231', '4312', '4321',
		);
		$plist = array ();
		$pass = '';

		$ord = $orders[mt_rand (0, count ($orders) - 1)];

		while (count ($plist) < $length) {
			foreach ($clist as $key => $value) {
				$plist[] = substr ($value, mt_rand (0, strlen ($value) - 1), 1);
				if (count ($plist) >= $length) {
					break;
				}
			}
		}

		$orders = array_reverse ($orders, true);
		$ord2 = $orders[mt_rand (0, count ($orders) - 1)];

		for ($j = 0; $j < 2; $j++) {
			for ($i = 0; $i < 4; $i++) {
				$one = $ord[$i] - 1;
				$two = $ord[$i] - 1 + 4;
				$pass .= $plist[$one];
				$pass .= $plist[$two];
			}
		}
		return substr ($pass, 0, $length);
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		if ($this->ignoreEmpty && (empty ($cgi->{$this->name}) || ! isset ($cgi->{$this->name}))) {
			return true;
		} else {
			return parent::validate ($value, $form, $cgi);
		}
	}

	/**
	 * Returns the display HTML for this widget.  The optional
	 * parameter determines whether or not to automatically display the widget
	 * nicely, or whether to simply return the widget (for use in a template).
	 * 
	 * @access	public
	 * @param	boolean	$generate_html
	 * @return	string
	 * 
	 */
	function display ($generate_html = 0) {
		global $intl;
		$attrstr = $this->getAttrs ();
		
		
		
		//Semias 29-5-2012 password helper
		$helpHTML = '';
		if($this->passwordHelp == true) {
			page_add_script (site_prefix () . '/js/jquery.pstrength-1.2.min.js'); // -min
			page_add_script('
			$(function() {
			$(\'#'.$this->_attrs['id'].'\').pstrength({
							minchar: '.$this->minLength.',
							verdects: ["'.intl_get('Very weak').'", "'.intl_get('Weak').'", "'.intl_get('Medium').'", "'.intl_get('Strong').'", "'.intl_get('Very strong').'", "'.intl_get('Unsafe password word!').'", "'.intl_get('Too short').'", "'.intl_get('Minimum number of characters is').'"]
						});
			$("#copybut").attr("disabled", true);
			
			$("#confirm").click(function() {
			  if($("#confirm").attr("checked")) {
				$("#copybut").attr("disabled", false);
			  } else {
				$("#copybut").attr("disabled", true);
			  }
			});
			
			});
			');
			page_add_style(site_prefix () . '/js/modalwindow.css');
			page_add_script(site_prefix () . '/js/modalwindow.js');
	
	$verifyFieldCode = '';
	if($this->verifyFieldId != '') {
		$verifyFieldCode = '$("#'.$this->verifyFieldId.'").val($("#pass").val());';
	}
			
			page_add_script('/* Password Generator, version 1.0a
   February 17, 2010 (adjusted password length)
   Version 1.0, January 19, 2010
   Will Bontrager
   http://www.willmaster.com/
   Copyright 2010 Bontrager Connection, LLC

   Bontrager Connection, LLC grants you a 
   royalty free license to use or modify 
   this software provided that this 
   copyright notice appears on all copies. 
   This software is provided "AS IS," 
   without a warranty of any kind.
*/
function GeneratePassword() {
var nc = "0123456789";
var lc = "abcdefghijklmnopqrstuvwxyz";
var uc = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
var oc = "-=[];\',./~!@#$%^&*()_+{}|:\"<>?";

var s = new String($("#custom").val());

if($("#fn").attr("checked")) { s += nc; } 
if($("#fl").attr("checked")) { s += lc; } 
if($("#fu").attr("checked")) { s += uc; } 
if($("#fo").attr("checked")) { s += oc; } 

var p = new String();
var slen = s.length;
if(slen) { p = s.charAt(Math.floor(Math.random()*slen)); }
s = new String($("#custom").val());
if($("#on").attr("checked")) { s += nc; } 
if($("#ol").attr("checked")) { s += lc; } 
if($("#ou").attr("checked")) { s += uc; } 
if($("#oo").attr("checked")) { s += oc; } 
slen = s.length;
if(slen) {
   for(var i=1; i<$("#len").val(); i++) {
      p += s.charAt(Math.floor(Math.random()*slen));
      }
   }
$("#pass").val(p);
}




function CopyToForm() {

if($("#confirm").attr("checked")) {

	$("#'.$this->_attrs['id'].'").val($("#pass").val());
	'.$verifyFieldCode.'
	$("#mask").trigger(\'click\');
	
	$("#confirm").attr("checked", false);
	$("#pass").val("");
	
	$("#copybut").attr("disabled", true);
	
	$("#'.$this->_attrs['id'].'").trigger(\'keyup\');
	
}
}


');
			
			$helpHTML = '<!-- #dialog is the id of a DIV defined in the code below -->
<a href="#dialog" name="modal" id="dialoglink">'.intl_get('Password Generator').'</a>
 
<div id="boxes">
 
     
    <!-- #customize your modal window here -->
 
    <div id="dialog" class="window">
        <b>'.intl_get('Password Generator').'</b>
        <!-- close button is defined as close class -->
        <a href="#" class="close">'.intl_get('Close').'</a>
		
		<div id="passgen">
<table border="0" cellpadding="0" cellspacing="5">
<tr>
<td>'.intl_get('First character').':</td>
<td><input type="checkbox" id="fu" value="yes" checked="checked">'.intl_get('Uppercase').' 
<input type="checkbox" id="fn" value="yes" checked="checked">'.intl_get('Number').'<br>
<input type="checkbox" id="fl" value="yes" checked="checked">'.intl_get('Lowercase').' 
<input type="checkbox" id="fo" value="yes">'.intl_get('Symbols').'</td>
</tr>
<tr>
<td>'.intl_get('Remaining characters').':</td>
<td><input type="checkbox" id="ou" value="yes" checked="checked">'.intl_get('Uppercase').' 
<input type="checkbox" id="on" value="yes" checked="checked">'.intl_get('Numbers').'<br>
<input type="checkbox" id="ol" value="yes" checked="checked">'.intl_get('Lowercase').' 
<input type="checkbox" id="oo" value="yes">'.intl_get('Symbols').'</td>
</tr>
<tr>
<td>'.intl_get('Custom characters').':</td>
<td><input type="text" id="custom" size="8" style="width:200px;"></td>
</tr>
<tr>
<td>'.intl_get('Password length').':</td>
<td><input type="text" id="len" size="8" style="width:100px;" value="'.($this->minLength+4).'"></td>
</tr>
<tr>
<td> </td>
<td><input type="button" value="'.intl_get('Generate').'" onclick="GeneratePassword()"></td>
</tr>
<tr>
<td> </td>
<td> </td>
</tr>
<tr>
<td>'.intl_get('Generated password').':</td>
<td><input type="text" id="pass" size="25" style="width:200px;" onclick="select()"></td>
</tr>
<tr>
<td> </td>
<td><input type="checkbox" id="confirm" name="confirm" /> <label for="confirm">'.intl_get('I have written down the password').'</label></td>
</tr>
<tr>
<td> </td>
<td><input type="button" id="copybut" value="'.intl_get('Copy to form').'" onclick="CopyToForm()"></td>
</table>

		</div>
 
    </div>
 
     
    <!-- Do not remove div#mask, because you\'ll need it to fill the whole screen --> 
    <div id="mask"></div>
</div>';
		}
		
		if ($generate_html) {
			return "\t" . '<tr' . $this->getClasses () . '>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $this->display_value . '</label></td>' . "\n\t\t" .
				'<td class="field"><input type="password" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) .
				'" ' . $this->extra . ' />'.$helpHTML.'</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="password" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />'.$helpHTML;
		}
	}
}



?>
