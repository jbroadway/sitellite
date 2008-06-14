<?php

class XedImporterForm extends MailForm {
	function XedImporterForm () {
		parent::MailForm ();

		$this->extra = 'enctype="multipart/form-data"';
		$this->uploadFiles = false;

		$w =& $this->addWidget ('hidden', 'ifname');

		$w =& $this->addWidget ('file', 'doc');
		$w->alt = intl_get ('Word Document');

		$w =& $this->addWidget ('submit', 'submit_button');
		$w->setValues (intl_get ('Import'));

		page_title (intl_get ('Word Importer'));
	}

	function onSubmit ($vals) {
		ob_start ();
		passthru (
			appconf ('wvhtml_location') . ' --targetdir=cache ' . escapeshellarg ($vals['doc']->tmp_name) . ' -'
		);
		$html = ob_get_contents ();
		ob_end_clean ();

		list ($one, $two) = explode ('<!--Section Begins-->', $html);
		list ($two, $three) = explode ('<!--Section Ends-->', $two);

		loader_import ('saf.HTML.Messy');
		$messy = new Messy ();
		$two = $messy->clean ($two);

		//echo '<pre>' . htmlentities ($two); exit;

		$two = str_replace ('<p><div', '<div', $two);
		$two = str_replace ('</div></p>', '</div>', $two);

		$vals['doc'] = $two;

		echo template_simple ('importer.spt', $vals);
		exit;
	}
}

function xed_filter_html ($doc) {
	return str_replace (
		array ("\r", "\n", "'",),
		array ("", "\\n", "\\'",),
		$doc
	);
}

?>