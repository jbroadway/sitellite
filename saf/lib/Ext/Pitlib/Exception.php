<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * PHP Image Transformation Library
 *
 * Pitlib is a PHP (PHP5) image processing solution, derived from Asido.
 *
 * @author Charles Brunet <cbrunet@php.net>
 * @license http://opensource.org/licenses/lgpl-license.php
 *     GNU Lesser General public License Version 2.1
 *
 * @package Pitlib
 * @subpackage Pitlib.Exception
 * @version 0.1.0
 */

/**
 * Generic exception class for Pitlib
 *
 * @package Pitlib
 * @subpackage Pitlib.Exception
 */
class Pitlib_Exception extends Exception {

}

/**
 * Exception class when operation not supported by driver
 *
 * @package Pitlib
 * @subpackage Pitlib.Exception
 */
class Pitlib_Exception_OperationNotSupported extends Pitlib_Exception {

}

?>
