<?php

define ('FPDF_FONTPATH', site_docroot () . '/inc/app/sitesearch/lib/Ext/fpdf/font/');
//loader_import ('sitesearch.Ext.fpdf');
loader_import ('sitesearch.Ext.fpdi');

class PDF_Extractor_fpdi extends fpdi {
	var $error_msg = false;

	function error ($msg) {
		$this->error_msg = $msg;
		return false;
	}
}

class PDF_Extractor extends SiteSearch_Extractor {
	var $mime = 'application/pdf';
	var $supply = 'file';

	function process ($file) {
		$pdf = new PDF_Extractor_fpdi ();
		$pagecount = $pdf->setSourceFile ($file);
		if ($pdf->error_msg) {
			return '';
		}

		$body = '';

		for ($i = 1; $i <= $pagecount; $i++) {
			$tpl = $pdf->ImportPage ($i);
			$body .= ' ' . $pdf->tpls[$tpl]['buffer'];
		}

		$text = '';

		$body = str_replace ('\\(', '[', $body);
		$body = str_replace ('\\)', ']', $body);
		preg_match_all ('|\((.*?)\)(\]TJ)?|', $body, $regs, PREG_SET_ORDER);
		foreach ($regs as $row) {
			$text .= $row[1];
			if (isset ($row[2])) {
				$text .= ' ';
			}
		}

		return $text;
	}
}

?>