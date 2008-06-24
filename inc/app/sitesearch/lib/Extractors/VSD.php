<?php

class VSD_Extractor extends SiteSearch_Extractor {
	var $mime = 'application/vnd.visio';

	function process ($text) {
		$res = preg_replace ('|[\r\t\n ]+|s', ' ', $text);
		$new = '';
		foreach (explode (' ', $res) as $item) {
			if (preg_match ('|[^a-zA-Z0-9\'"\(\)/\.\,_-]|', $item)) {
				continue;
			}
			if (strpos ($item, '_Toc') !== false) {
				continue;
			}
			$new .= ' ' . $item;
		}
		$new = str_replace (
			array ('MSWordDoc', 'Microsoft Word Document', 'Word Document', 'Microsoft Word', 'urn schemas-microsoft-com office smarttags', 'Dr Eric Franz', 'Microsoft Excel', 'Worksheets', 'MbP', 'Sheet1', 'Sheet2', 'Sheet3', 'Sheet4', 'Sheet5', 'Sheet6', 'Sheet7', 'Sheet8', 'Microsoft Powerpoint', 'PAGEREF', 'HYPERLINK', 'EMBED', 'Visio'),
			array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
			$new
		);
		return $new;
	}
}

?>