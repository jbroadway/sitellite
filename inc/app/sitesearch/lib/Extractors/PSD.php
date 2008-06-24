<?php

class PSD_Extractor extends SiteSearch_Extractor {
	var $mime = 'image/x-photoshop';

	function process ($text) {
		if (preg_match ('|<rdf:RDF(.*?)</rdf:RDF>|s', $text, $regs)) {
			return preg_replace (
				'/[\r\n\t ]+/',
				' ',
				strip_tags ('<rdf:RDF' . $regs[1] . '</rdf:RDF>')
			);
		}
		return '';
	}
}

?>