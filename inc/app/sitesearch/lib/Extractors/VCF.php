<?php

class VCF_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/x-vcard';

	function process ($text) {
		return $text;
	}
}

?>