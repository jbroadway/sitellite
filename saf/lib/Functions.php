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
// This is a list of functions aimed at improving something about existing
// PHP functions, and which are used in places in the Sitellite Application
// Framework.
//

/**
 * This is a list of functions aimed at improving something about existing
 * PHP functions, and which are used in places in the Sitellite Application
 * Framework.
 *
 * @package Functions
 *
 */

/**
 * Where htmlentities() causes corruption of data in alternate character
 * sets, this function aims to translate the necessary entities without
 * damaging the data.  Currently does a str_replace for the following
 * entities:
 * - '&amp;' * => '&amp;amp;'
 * - '&lt;' * => '&amp;lt;'
 * - '&gt;' * => '&amp;gt;'
 * - '<' *  * => '&lt;'
 * - '>' *  * => '&gt;'
 * - '"' *  * => '"'
 *
 * It also does an extra preg_replace for & followed by a
 * space/tab/cr/lf and replaces the & with a &amp;.
 *
 * @access public
 * @param string
 * @param string
 * @param string
 * @return string
 */
function htmlentities_compat ($val1, $val2 = ENT_QUOTES, $val3 = 'iso-8859-1') {
	$val1 = htmlspecialchars ($val1, $val2, $val3);
	return preg_replace_callback (
		'!&amp;#((?:[0-9]+)|(?:x(?:[0-9A-F]+)));?!i', 'htmlentities_compat_decode', $val1);
	/*$val1 = preg_replace (
		'/&([a-zA-Z0-9]+);/',
		'&amp;\1;',
		$val1
	);
	$val1 = str_replace (
		array (
			'<',
			'>',
			'"',
		),
		array (
			'&lt;',
			'&gt;',
			'"',
		),
		$val1
	);
	$val1 = preg_replace ('/&([ \t\r\n])/s', '&amp;\1', $val1);
	return $val1;*/
}

/**
 * Callback for fixing unicode entities for htmlentities_compat().
 *
 * @access public
 * @param array
 * @return string
 */
function htmlentities_compat_decode ($matches) {
	if (! is_int ($matches[1]{0})) {
		$val = '0' . $matches[1] + 0;
	} else {
		$val = (int) $matches[1];
	}

	if ($val > 255) {
		return '&#' . $matches[1] . ';';
	}
	if (($val >= 65 && $val <= 90) ||
		($val >= 97 && $val <= 122) ||
		($val >= 48 && $val <= 57)) {
			return chr ($val);
	}
	return $matches[0];
}

/**
 * Reverses the htmlentities_compat() function exactly.
 *
 * @access public
 * @param string
 * @return string
 */
function htmlentities_reverse ($val1) {
	$val1 = str_replace (
		array (
			'&lt;',
			'&gt;',
			'"',
			'&amp;',
			'&amp;amp;',
			'&amp;lt;',
			'&amp;gt;',
		),
		array (
			'<',
			'>',
			'"',
			'&',
			'&amp;',
			'&lt;',
			'&gt;',
		),
		$val1
	);
	//$val1 = preg_replace ('/&amp;([ \t\r\n])/s', '&\1', $val1);
	return $val1;
}

/**
 * Note: Deprecated in favour of a simple:
 *
 * $obj = (object) $array;
 *
 * Creates an object out of a hash (or any other type).  There is no make_hash()
 * function because that can simply be cast as well ($array = (array) $obj).
 * Thanks to Demian Turner for pointing out this equivalence.
 *
 * @access public
 * @param array
 * @return object
 */
function &make_obj ($array) {
	return (object) $array;
}

/**
 * Creates a single-level associative array out of an array of objects
 * or an array of associative arrays.  $key and $value specify the
 * property or array key name to use for their respective values in
 * the new associative array.
 *
 * @access public
 * @param array
 * @param string
 * @param string
 * @return array hash
 */
function make_assoc ($array, $key = '', $value = '') {
	$assoc = array ();

	// accept single objects too (compatible with $db->fetch ())
	if (is_object ($array)) {
		$array = array ($array);
	}

	// if $key or $value aren't set, default them to the first two properties or keys
	// in current ($array)
	if (empty ($key) || empty ($value)) {
		if (is_object (current ($array))) {
			$properties = array_keys (get_object_vars (current ($array)));
		} else {
			$properties = array_keys ($array);
		}
		if (empty ($key)) {
			$key = array_shift ($properties);
		}
		if (empty ($value)) {
			$value = array_shift ($properties);
		}
	}
	foreach ($array as $obj) {
		if (is_object ($obj)) {
			$assoc[$obj->{$key}] = $obj->{$value};
		} else {
			$assoc[$obj[$key]] = $obj[$value];
		}
	}
	return $assoc;
}

/**
 * Makes sure that the salt is always two characters in length before
 * sending it to the crypt() function.  Also generates its own two
 * character salt if none is provided.
 *
 * @access public
 * @param string
 * @param string
 * @return string
 */
function better_crypt ($pass, $salt = '') {
	if (empty ($salt)) {
		$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$salt .= substr ($alpha, mt_rand (0, strlen ($alpha) - 1), 1);
		$salt .= substr ($alpha, mt_rand (0, strlen ($alpha) - 1), 1);
	} else {
		$salt = substr ($salt, 0, 2);
	}
	return crypt ($pass, $salt);
}	

/**
 * Calls the better_crypt() function instead of crypt() when comparing 
 * a new password to the original to see if it matches up.
 *
 * @access public
 * @param string
 * @param string
 * @return boolean
 */
function better_crypt_compare ($pass, $original) {
	if (better_crypt ($pass, $original) == $original) {
		return true;
	}
	return false;
}

if (PHP_VERSION < '4.1.0') {
	/**
	 * If the PHP version is less than 4.1.0, emulates the vsprintf() function
	 * using a call_user_func_array() with the sprintf() function.  The use of this
	 * function in the saf.I18n package was causing fatal errors in Sitellite on
	 * earlier PHP versions.
	 *
	 * @access public
	 * @param string
	 * @param array
	 * @return string
	 */
	function vsprintf ($str, $args) {
		array_unshift ($args, $str);
		return call_user_func_array ('sprintf', $args);
	}
}

if (! function_exists ('is_a')) {
/**
 * If the PHP version is less than 4.2.0, emulates the is_a() function,
 * which determines whether the object, $class, is of the class specified
 * by $match.
 *
 * @access public
 * @param string
 * @param string
 * @return boolean
 */
function is_a ($class, $match) {
	if (empty ($class)) {
		return false;
	}
	$class = is_object ($class) ? get_class ($class) : $class;
	if (strtolower ($class) == strtolower ($match)) {
		return true;
	}
	return is_a (get_parent_class ($class), $match);
}
}

/**
 * Creates an HTML comment padded with dashes for easily marking sections
 * of a document.  Hnady for viewing the source of large documents.
 *
 * @access public
 * @param string
 * @return string
 */
function html_marker ($note) {
	if (conf ('Server', 'debug')) {
		return NEWLINEx2 . '<!--' . str_pad ($note, 75, ' ', STR_PAD_BOTH) . '-->' . NEWLINEx2;
	} else {
		return '';
	}
}

if (PHP_VERSION < '4.0.5') {
	/**
	 * If the PHP version is less than 4.0.5, emulates the array_search()
	 * function.
	 *
	 * @access public
	 * @param mixed
	 * @param array
	 * @return mixed
	 */
	function array_search ($needle = '', $haystack = '') {
		while (list ($key, $val) = each ($haystack)) {
			if ($needle == $val) {
				return $key;
			}
		}
		return 0;
	}
}

/**
 * Returns a file size formatted in a more human-friendly format, rounded
 * to the nearest Gb, Mb, Kb, or byte.
 *
 * @access public
 * @param integer
 * @return string
 */
function format_filesize ($size = 0) {
	if ($size >= 1073741824) {
		return round ($size / 1073741824 * 10) / 10 . " Gb";
	} elseif ($size >= 1048576) {
		return round ($size / 1048576 * 10) / 10 . " Mb";
	} elseif ($size >= 1024) {
		return round ($size / 1024) . " Kb";
	} else {
		return $size . " b";
	}
}

/**
 * Adds commas every three numbers from the right of the period to a given
 * number.
 *
 * @access public
 * @param string
 * @return string
 */
function commify ($number = "") {
	$number = strrev ($number);
	$number = preg_replace ("/(\d\d\d)(?=\d)(?!\d*\.)/", "\\1,", $number);
	return strrev ($number);
}

/**
 * Wraps a print_r() or var_dump() of the given $value with a set of <pre></pre>
 * tags around it, and echoes it.
 *
 * @access public
 * @param mixed
 * @param boolean
 *
 */
function info ($value, $full = false) {
	if (php_sapi_name () !== 'cli') {
		echo '<pre>';
		if ($full) {
			var_dump ($value);
		} else {
			print_r ($value);
		}
		echo '</pre>';
	} else {
		if ($full) {
			var_dump ($value);
		} else {
			print_r ($value);
		}
	}
}

/**
 * Turns a regular array into an associative array with a $value => $value
 * new key/value relation.  For example, an array [1=>Joe,2=>Jane,3=>Joe]
 * would become [Joe=>Joe,Jane=>Jane].  Please note the loss of duplicate
 * values.
 *
 * @access public
 * @param array
 * @return array hash
 */
function assocify ($arr) {
	$new = array ();
	foreach ($arr as $val) {
		$new[$val] = $val;
	}
	return $new;
}

if (PHP_VERSION < '4.2.0') {
	/**
	 * Provides array_chunk() for PHP 4.1 users.
	 *
	 * @access public
	 */
	function array_chunk ($input, $size, $preserve_keys = false) {
		@reset ($input);

		$i = $j = 0;

		while (@list (,$value) = @each ($input)) {
			if (! (isset ($chunks[$i]))) {
				$chunks[$i] = array();
			}

			if (count ($chunks[$i]) < $size) {
				if ($preserve_keys) {
					$chunks[$i][$j] = $value;
					$j++;
				} else {
					$chunks[$i][] = $value;
				}
			}  else {
				$i++;

				if ($preserve_keys) {
					$chunks[$i][$j] = $value;
					$j++;
				} else {
					$j = 0;
					$chunks[$i][$j] = $value;
				}

			}
		}

		return $chunks;
	}

	/**
	 * @global int CASE_UPPER uppercase array kes
	 */
	define('CASE_UPPER', 1);

	/**
	 * @global int CASE_LOWER lowercase array keys
	 */
	define('CASE_LOWER', 2);

	/**
	 * Returns an array with all string keys lowercased or uppercased
	 *
	 * @access public
	 * @see http://www.php.net/array_change_key_case
	 * @param array  array to change keys
	 * @param int	how to transform keys
	 * @return array
	 */
	function array_change_key_case ($array, $changeCase = CASE_LOWER) {
		if (is_array ($array)) {
			foreach ($array as $key => $value) {
				switch ($changeCase) {
					case CASE_LOWER:
						$return[strtolower ($key)] = $value;
						break;
					case CASE_UPPER:
						$return[strtoupper ($key)] = $value;
						break;
				}
			}
			return $return;
		} else {
			return $array;
		}
	}
}

/**
 * Calls array_chunk(), but adds a parameter $fill, which "fills out"
 * the potentially uneven number if items in the last array with the
 * value of $fill.
 *
 * @access public
 * @param array
 * @param integer
 * @param boolean
 * @param mixed
 * @return array
 */
function array_chunk_fill ($list, $chunk, $assoc = false, $fill = '&nbsp;') {
	$list = array_chunk ($list, $chunk, $assoc);
	$s =& $list[count ($list) - 1];
	if (count ($list) > 0 && count ($s) < $chunk) {
		for ($i = count ($s); $i < $chunk; $i++) {
			$s[] = $fill;
		}
	}
	return $list;
}

/**
 * Converts entities to unicode entities (ie. < becomes &#60;).
 * From php.net/htmlentities comments, user "webwurst at web dot de"
 *
 * @access public
 * @param string
 * @param string
 * @return string
 */
function xmlentities ($string, $quote_style = ENT_COMPAT) {
	$trans = get_html_translation_table (HTML_ENTITIES, $quote_style);

	foreach ($trans as $key => $value) {
		$trans[$key] = '&#' . ord ($key) . ';';
	}

	return strtr ($string, $trans);
}

/**
 * Converts XML unicode entities back to their original characters
 * (ie. &#60; becomes <).
 *
 * @access public
 * @param string
 * @return string
 */
function xmlentities_reverse ($string) {
	return preg_replace_callback (
		'/&#([^;]+);/',
		create_function (
			'$matches',
			'return chr ($matches[1]);'
		),
		$string
	);
}

/**
 * Determines the content type of a file based on a small list of common types.
 * Useful when mime_content_type() isn't available or isn't configured
 * correctly.
 *
 * @access public
 * @param string
 * @return string
 */
function mime ($file, $default = 'text/plain') {
	$types = array (
		'html' => 'text/html',
		'xml' => 'text/xml',
		'txt' => 'text/plain',
		'vcf' => 'text/x-vcard',
		'ics' => 'text/calendar',
		'rtf' => 'text/rtf',
		'pdf' => 'application/pdf',
		'mp3' => 'audio/mpeg',
		'doc' => 'application/msword',
		'xls' => 'application/xls',
		'ppt' => 'application/ppt',
		'psd' => 'image/x-photoshop',
		'bin' => 'application/octet-stream',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'cpio' => 'application/x-cpio',
		'latex' => 'application/x-latex',
		'tar' => 'application/x-tar',
		'tex' => 'application/x-tex',
		'texinfo' => 'application/x-texinfo',
		'texi' => 'application/x-texinfo',
		'xml' => 'application/xml',
		'zip' => 'application/zip',
		'wav' => 'audo/x-wav',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'html' => 'text/html',
		'htm' => 'text/html',
		'mpg' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'avi' => 'video/avi',
		'dot' => 'application/msword',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/octet-stream',
		'wma' => 'audio/x-ms-wma',
		'wmv' => 'video/x-ms-wmv',
		'swf' => 'application/x-shockwave-flash',
		'chm' => 'application/mshelp',
		'dat' => 'application/octet-stream',
		'flv' => 'video/x-flv',
		'wav' => 'audio/x-wav',
		'gz' => 'application/gzip',
		'tar' => 'application/tar',
		'jar' => 'application/x-compressed',
		'mcd' => 'application/mcad',
		'asf' => 'audio/asf',
		'rmvb' => 'audio/x-pn-realaudio-plugin',
		'divx' => 'video/divx',
		'mp4' => 'audio/mp4',
		'm4p' => 'audio/x-m4p',
		'm4v' => 'video/mp4',
		'm4a' => 'audio/mp4',
		'bmp' => 'image/bmp',
		'3gp' => 'movie/3gp',
		'torrent' => 'application/x-bittorrent',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'svg' => 'image/svg+xml',
		'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
		'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
		'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
		'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
		'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
		'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
		'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
		'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
	);
	$ext = strtolower (array_pop (explode ('.', basename ($file))));
	if (isset ($types[$ext])) {
		return $types[$ext];
	}
	//return exec ('file -bi ' . escapeshellarg ($file));
	return $default;
}

if (! function_exists ('mime_content_type')) {
	function mime_content_type ($file) {
		return mime ($file);
	}
}

/**
 * Determines whether the specified variable is an associative array.
 *
 * @access public
 * @param mixed
 * @return boolean
 */
function is_assoc ($arr) {
	if (! is_array ($arr)) {
		return false;
	}
	$i = 0;
	foreach (array_keys ($arr) as $k) {
		if ($k !== $i) {
			return true;
		}
		$i++;
	}
	return false;
}

/**
 * Splits an SQL script into distinct queries which can be evaluated or
 * manipulated individually.
 *
 * @access public
 * @param string
 * @return array
 */
function sql_split ($sql) {
	$out = array ('');
	$broken = preg_split ('/[\n\r]+/s', $sql);
	foreach ($broken as $row) {
		$row = trim ($row);
		if (strpos ($row, '#') === 0 || strpos ($row, '--') === 0 || empty ($row)) {
			continue;
		} elseif (preg_match ('/;$/', $row)) {
			$out[count ($out) - 1] .= substr ($row, 0, strlen ($row) - 1) . "\n";
			$out[] = '';
		} else {
			$out[count ($out) - 1] .= $row . "\n";
		}
	}
	if (empty ($out[count ($out) - 1])) {
		array_pop ($out);
	}
	return $out;
}

/**
 * strrpos() which allows multiple characters in the needle.
 *
 * @access public
 * @param string
 * @param string
 * @return int
 */
function better_strrpos ($haystack, $needle) {
	$pos_rule = ($offset < 0) ? strlen ($haystack) + ($offset - 1) : $offset;
	$last_pos = false;
	$first_run = true;
	do {
		$pos = strpos ($haystack, $needle, (intval ($last_pos) + (($first_run) ? 0 : strlen ($needle))));
		if ($pos !== false && (($offset < 0 && $pos <= $pos_rule) || $offset >= 0)) {
			$last_pos = $pos;
		} else {
			break;
		}
		$first_run = false;
	} while ($pos !== false);
	if ($offset > 0 && $last_pos < $pos_rule) {
		$last_pos = false;
	}
	return $last_pos;
}

if (version_compare (phpversion (), '5.0') < 0) {
	eval('
		function clone ($object) {
			return $object;
		}
	');
}

/**
 * A date() replacement which respects your setlocale() setting.
 * From php.net/date comments, user "isp dot phptime at spsoft dot de"
 *
 * @access public
 * @param string
 * @param int
 * @return string
 */
function localdate ($format, $unixdate = '') {
	if ($unixdate == '') {
		$unixdate = time ();
	}

	$format = str_replace (
		array (
			'D', // Mon-Sun
			'l', // Sunday-Saturday
			'F', // January-December
			'M', // Jan-Dec
			'c', // Preferred date/time format in current locale
			'x', // Preferred date format
			'X', // Preferred time format
		),
		array (
			'%\\a', // Mon-Sun
			'%\\A', // Sunday-Saturday
			'%\\B', // January-December
			'%\\b', // Jan-Dec
			'%\\c', // Preferred date/time format in current locale
			'%x', // Preferred date format
			'%X', // Preferred time format
		),
		$format
	);

	$ret = date ($format, $unixdate);

	if (strpos ($ret, '%') === false) {
		return $ret; // no placeholders, so no non-english text
	}
	return strftime ($ret, $unixdate);
}

/**
 * Replacement for readfile() in PHP5 which doesn't encounter memory limits.
 *
 * @access public
 * @param string
 * @param boolean
 * @return int
 */
function readfile_chunked ($filename, $retbytes = true) {
        $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
        $buffer = '';
        $cnt = 0;
        // $handle = fopen($filename, 'rb');
        $handle = fopen ($filename, 'rb');
        if ($handle === false) {
                return false;
        }
        while (! feof ($handle)) {
                $buffer = fread ($handle, $chunksize);
                echo $buffer;
                ob_flush ();
                flush ();
                if ($retbytes) {
                        $cnt += strlen ($buffer);
                }
        }
        $status = fclose ($handle);
        if ($retbytes && $status) {
                return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
}

/**
 * Replacement for mail() in Sitellite, using Sitellite's internal messaging
 * system instead.  The first parameter is a username instead of an email address,
 * and the fourth is a sender's username instead of a From header.
 *
 * @access public
 * @param string
 * @param string
 * @param string
 * @param string
 * @return boolean
 */
function sitellite_mail ($to_user, $subject, $body, $from_user = '') {
	loader_import ('cms.Workspace.Message');
	$msg = new WorkspaceMessage ();
	return $msg->send (
		$subject,
		$body,
		$to_user,
		array (),
		0,
		'normal',
		$from_user
	);
}

function site_mail ($to , $subject , $body , $from = "" , $extra = array() , $attach = array() ) {

    // load config file
    $config = parse_ini_file("inc/conf/config.ini.php");

    // check additional parameters
    if(isset($extra['template'])) {
        $config['template'] = $extra['template'];
    }

    if(isset($extra['use_abuse'])) {
        $config['use_abuse'] = $extra['use_abuse'];
    }

    if(isset($extra['use_html'])) {
        $config['use_html'] = $extra['use_html'];
    }

    // set mta_x vars
    $config['mta_mail'] = array();
    $config['mta_smtp'] = array();
    $config['mta_sendmail'] = array();
    $config['mta_qmail'] = array();

    // load the phpmailer package
	loader_import ('ext.phpmailer');

	// configure phpmailer
	$msg = new phpmailer ();

    $msg->CharSet = "UTF-8";

	switch ($config['mta']) {
		case 'smtp':
			$msg->IsSMTP ();
/*			foreach ( $config ['mta_smtp'] as $k => $v) {
				$msg->{$k} = $v;
			} */
			break;
      	case 'sendmail':
      		$msg->IsSendmail ();
/*      		foreach ( $config ['mta_sendmail'] as $k => $v) {
      			$msg->{$k} = $v;
      		} */
      		break;
      	case 'qmail':
      		$msg->IsQmail ();
/*      		foreach ( $config ['mta_qmail'] as $k => $v) {
      			$msg->{$k} = $v;
      		} */
      		break;
      	case 'mail':
      	default:
      		$msg->IsMail ();
/*      		foreach ( $config ['mta_mail'] as $k => $v) {
      			$msg->{$k} = $v;
      		} */
	}

	// use HTML ?

    $msg->IsHTML ( true );

    $msg->Subject = $subject;

    $template = file_get_contents("inc/html/mail/" . $config['template']);
  	$body = template_simple ( $template , array ('body' => $body  ) );

    $msg->Body = $body;
    $msg->AltBody = preg_replace (
    	array (
    		"/^[\r\n]+/",
    		"/[\r\n]+$/",
    		"/[\r\n][\r\n]+/",
    	),
    	array (
    		"",
    		"",
    		"\n\n",
    	),
    	strip_tags (
    		str_replace (
    			array (
    				'<hr />',
    				'<hr>',
    				'&nbsp;',
    				'&copy;',
    				'&lt;',
    				'&gt;',
    			),
    			array (
    				'--',
    				'--',
    				' ',
    				'(c)',
    				'<',
    				'>',
    			),
    			$msg->Body
    		)
    	)
    );

    // Get right sender
    if(!empty($from)) {
        // "convert" $from parameter
        $str = array();
        $str = explode(":",$from,2);

        if(count($str) == 1) {
         $msg->From = trim($str[0]);
         $msg->FromName = trim($str[0]);
        } else {
         $msg->From = trim($str[1]);
         $msg->FromName = trim($str[1]);
        }
    } else {
      if( ! empty ( $extra['from_email'] ) ) {
        $msg->From = $extra['from_email'];
        $msg->FromName = empty($extra['from_name']) ? $extra['from_email'] : $extra['from_name'];
      } else {
 	    $msg->From = $config['from_email'];
        $msg->FromName = $config['from_name'];
      }
    }

    # CHECK E-MAIL ABUSE SYSTEM

    // multiple TO: users
    if(!is_array($to )) {
     $to = array( $to );
    }

   	// send message to each recipient
	$sent = 0;
	$total = count ($to);

    // CC
    if(!empty( $extra['CC'] )) {
     if(!is_array($extra['CC'])) {
       $extra['CC'] = array ($extra['CC']);
     }
     foreach($extra['CC'] as $cc) {
      $msg->AddCC ( $cc );
     }
    }

    // BCC
    if(!empty( $extra['BCC'] )) {
     if(!is_array($extra['BCC'])) {
      $extra['BCC'] = array ($extra['BCC']);
     }
     foreach($extra['BCC'] as $bcc) {
      $msg->AddBCC ( $bcc );
     }
    }

    // Attachment
    if(!empty($attach)) {
     foreach($attach as $attachment) {
      if(empty($attachment['encoding'])) {
       $attachment['encoding'] = "base64";
      }
      if(empty($attachment['type'])) {
       $attachment['type'] = 'application/octet-stream';
      }

      $msg->AddAttachment ( $attachment['path'] , $attachment['name'] , $attachment['encoding'] , $attachment['type'] );
     }
    }


	foreach ($to as $recipient) {
     $msg->AddAddress ($recipient);
	 set_time_limit (30);
	 if ($msg->Send ()) {
	 	$sent++;
        //				if ($total > 1000 && $sent % (floor ($this->total / 25)) == 0) {
        //					$spush->send ('<p>' . intl_getf ('Status: %s out of %s messages sent.  Continuing...', $sent, $total) . '</p>');
        //				}
	 } else {
        //				$this->error = $mail->ErrorInfo;
        echo '<p>' . intl_get ('An error occurred') . ': ' . $msg->ErrorInfo . ' </p>';
		sleep (5);
	 }
     $msg->ClearAddresses ();
	}

    return true;

}
?>