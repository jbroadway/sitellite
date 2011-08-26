<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: Functions.php,v 1.1 2008/02/20 10:29:16 lux Exp $


/**
 * Replace str_word_count()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.str_word_count
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 4.3.0
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('str_word_count'))
{
    function str_word_count($string, $format = null)
    {
        if ($format != 1 && $format != 2 && $format !== null) {
            trigger_error("str_word_count() The specified format parameter, '$format' is invalid", E_USER_WARNING);
            return false;
        }

        $word_string = preg_replace('/[0-9]+/', '', $string);
        $word_array  = preg_split('/[^A-Za-z0-9_\']+/', $word_string, -1, PREG_SPLIT_NO_EMPTY);

        switch ($format) {
            case null:
                return count($word_array);
                break;

            case 1:
                return $word_array;
                break;

            case 2:
                $lastmatch = 0;
                $word_assoc = array();
                foreach ($word_array as $word) {
                    $word_assoc[$lastmatch = strpos($string, $word, $lastmatch)] = $word;
                }
                return $word_assoc;
                break;
        }
    }
}

/**
 * Converts a unicode character number to the proper UTF-8 value.
 *
 * Borrowed from:
 *
 * http://www.randomchaos.com/document.php?source=php_and_unicode
 */
function unicode_chr ($unicode) {
	if ( $unicode < 128 ) {

		return chr ( $unicode );

	} elseif ( $unicode < 2048 ) {

		return chr ( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) )
			. chr ( 128 + ( $unicode % 64 ) );

	} else {

		return chr ( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) )
			. chr ( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) )
			. chr ( 128 + ( $unicode % 64 ) );

	}
}

/**
 * Decodes URLs that contain unicode values.
 */
function unicode_decode ($txt) {
	return preg_replace_callback (
		'|%u([0-9A-Z]{4})|s',
		create_function (
			'$matches',
			'return unicode_chr (hexdec ($matches[1]));'
		),
		$txt
	);
}

/**
 * Decodes URLs that contain unicode values into their equivalent html entities.
 */
function unicode2htmlentities ($txt) {
	return preg_replace_callback (
		'|%u([0-9A-Z]{4})|s',
		create_function (
			'$matches',
			'return \'&#\' . hexdec ($matches[1]) . \';\';'
		),
		$txt
	);
}

?>