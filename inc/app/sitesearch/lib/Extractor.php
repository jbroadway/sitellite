<?php

/**
 * Base class for writing SiteSearch file content extractors.
 *
 */
class SiteSearch_Extractor {
	/**
	 * The mime type for documents that are handled by your extractor.
	 *
	 */
	var $mime = 'application/x-octet-stream';

	/**
	 * Whether to supply the 'text' (contents) of the document, or the
	 * name of the file itself.  Override and set to 'file' if your
	 * extractor should be given the file name instead of its contents.
	 * Note that you will have to get the file contents yourself during
	 * your process() method.
	 *
	 */
	var $supply = 'text';

	/**
	 * Retrieves the contents of the specified file.
	 *
	 */
	function getFileContents ($file) {
		return @join ('', @file ($file));
	}

	/**
	 * Removes any lingering characters that might affect the indexer.
	 * Note that the '\w' in the preg_replace() call is locale-sensitive,
	 * so if you have the locale set wrong it may strip out what are
	 * legitimate characters.
	 *
	 */
	function removeInvalidChars ($text) {
		return preg_replace ('/[^\w]+/s', ' ', $text);
	}

	/**
	 * Determines which extractor should handle the specified file.
	 *
	 */
	function getHandler ($file) {
		$info = @pathinfo ($file);
		$ext = strtoupper ($info['extension']);
		if (@file_exists ('inc/app/sitesearch/lib/Extractors/' . $ext . '.php')) {
			return $ext;
		}
		return 'Default';
	}

	/**
	 * Performs the extraction.  Calls the appropriate extractor to handle
	 * most of the job.  If $name is passed a handler name, then $file is
	 * assumed to be the contents and not the file name.  Otherwise, it
	 * depends on the $supply parameter of the extractor.
	 *
	 */
	function extract ($file, $name = false) {
		if (! $name) {
			$extractor_name = SiteSearch_Extractor::getHandler ($file);
		} else {
			$extractor_name = SiteSearch_Extractor::getHandler ('file.' . $name);
		}
		$class = $extractor_name . '_Extractor';
		loader_import ('sitesearch.Extractors.' . $extractor_name);
		$extractor = new $class;
		if ($extractor->supply != 'text' || $name != false) {
			$text = $extractor->process ($file);
		} else {
			$text = SiteSearch_Extractor::getFileContents ($file);
			$text = $extractor->process ($text);
		}
		return SiteSearch_Extractor::removeInvalidChars ($text);
		return $text;
	}

	/**
	 * This is the method you will need to override in order to write
	 * new extractors.  Depending on the value of the $supply property,
	 * this method receives either the contents of the file to extract
	 * the text from, or alternately the file name itself.
	 *
	 */
	function process ($text) {
		die ('You must override the process() method!');
	}
}

function extractor_run ($file, $name = false) {
	$e = new SiteSearch_Extractor;
	return $e->extract ($file, $name);
}

function extractor_remove_invalid_chars ($txt) {
	return SiteSearch_Extractor::removeInvalidChars ($txt);
}

?>