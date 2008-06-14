<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the Simian Open Software License.    |
// | Please see the accompanying file OPENLICENSE for licensing details!  |
// |																	  |
// | You should have received a copy of the Simian Open Software License  |
// | along with this program; if not, write to Simian Systems,			  |
// | 1071 Corydon Ave, Winnipeg, MB, R3M 0X3, CANADA.  The Simian		  |
// | Public License is also available at the following web site		      |
// | address: <http://www.simian.ca/index/license>						  |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>								  |
// +----------------------------------------------------------------------+
//
// htmldoc -- functions for interfacing with htmldoc command line tool
// htmldoc is available at http://www.htmldoc.org/
//

/**
 * Retrieves the generated PDF data as a string, based on HTML data passed
 * to htmldoc on STDIN.
 *
 * @param string
 * @param string
 * @return string
 */
function htmldoc ($html, $options = '') {
	ob_start ();
	passthru (
		'htmldoc --quiet --firstpage c1 -t pdf '
		. escapeshellarg ($options)
		. " - <<ITSTHEENDOFTHEWORLDASWEKNOWITANDIFEELFINE\n"
		. escapeshellarg ($html) . "\n"
		. "ITSTHEENDOFTHEWORLDASWEKNOWITANDIFEELFINE\n"
	);
	$pdf = ob_get_contents ();
	ob_end_clean ();
	return $pdf;
}

/**
 * Alias of the htmldoc() function for use with the modes.php file.
 * To create a PDF output mode, use the following INI block:
 *
 * <code>
 * [pdf]
 * content_type = application/pdf
 * filter 1 = "final: ext.htmldoc"
 * </code>
 *
 * @param string
 * @return string
 */
function ext_htmldoc_content_filter ($str) {
	return htmldoc ($str);
}

/**
 * Retrieves the generated PDF data as a string.
 *
 * @param string
 * @param string
 * @return string
 */
function htmldoc_get_pdf ($filename, $options = '') {
	$filename = htmldoc_verify_filename ($filename);
	ob_start ();
	passthru (
		escapeshellcmd (
			"htmldoc --quiet --firstpage c1 -t pdf $options $filename"
		)
	);
	$pdf = ob_get_contents ();
	ob_end_clean ();
	return $pdf;
}

/**
 * Generates a PDF out of the specified file (usually a URL) and sends
 * the appropriate HTTP headers to send it to the user's browser
 * automatically.
 *
 * @param string
 * @param string
 * @param string optional filename (unused at the moment)
 */
function htmldoc_send_pdf ($filename, $options = '', $saveas = false) {
	$filename = htmldoc_verify_filename ($filename);
	if ($saveas) {
		header ('Content-Type: application/x-octet-stream');
		header ('Content-Disposition: attachment; filename=' . htmldoc_url_filename ($filename));
	} else {
		header ('Content-Type: application/pdf');
	}
	passthru (
		escapeshellcmd (
			"htmldoc --quiet --firstpage c1 -t pdf $options $filename"
		)
	);
}

/**
 * Removes the extraneous stuff from the specified URL for use as
 * a file name for PDFs.
 *
 * @param string
 * @return string
 */
function htmldoc_url_filename ($file) {
	return preg_replace ('/_pdf$/i', '.pdf', str_replace (
		array (
			'http://',
			'/',
			'.',
			'?',
			'#',
			'&',
		),
		array (
			'',
			'_',
			'_',
			'_',
			'_',
			'_',
		),
		$file
	));
}

/**
 * Corrects the specified file name (aka URL) by checking if it is
 * a path or a complete URL.  Returns a complete URL either way.
 *
 * @param string
 * @return string
 */
function htmldoc_verify_filename ($file) {
	if (strpos ($file, '/') === 0) {
		return 'http://' . site_domain () . site_prefix () . $file;
	}
	return $file;
}

?>