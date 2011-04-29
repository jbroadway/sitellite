<?php

loader_import ('ext.Mp3.V2.id3v2');

class MP3_Extractor extends SiteSearch_Extractor {
	var $mime = 'audio/mpeg';
	var $supply = 'file';

	function process ($file) {
		$mp3 = new id3v2 ();
		$mp3->GetInfo ($file);
		if (! is_array ($mp3->id3v2Info)) {
			return false;
		}
	
		ob_start ();
		print_r ($mp3->id3v2Info);
		$out = ob_get_contents ();
		ob_end_clean ();
		return $out;
	}
}

?>