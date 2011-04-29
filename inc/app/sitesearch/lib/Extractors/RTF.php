<?php

class RTF_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/rtf';

	function process ($text) {
		$body = str_replace (
			array ('{', '}', "\\\n"),
			array (' ', ' ', "\n"),
			$text
		);
		$body = preg_replace ('|\\\([^;]+?);|s', ' ', $body);
		$body = preg_replace ('|\\\[\'a-zA-Z0-9]+|s', ' ', $body);
		return $body;
	}
}

?>