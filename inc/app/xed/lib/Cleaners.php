<?php

$GLOBALS['TIDY_PATH'] = false;

function the_cleaners ($data, $wrap = true, $safe = true) {
	global $TIDY_PATH;
	if ($TIDY_PATH) {
		ob_start ();
		passthru ($TIDY_PATH . " -asxhtml -icq -f /dev/null 2>&1 <<END-XED-INPUT\n" . $data . "\nEND-XED-INPUT");
		$data = ob_get_contents ();
		ob_end_clean ();
		if ($wrap) {
			$data = the_cleaners_strip_tidy_output ($data);
		}
		return $data;
	} else {
		if ($wrap) {
			$data = the_cleaners_wrapper ($data);
		}
		loader_import ('saf.HTML.Messy');
		$messy = new Messy ();
		$messy->safe = $safe;
		$data = $messy->clean ($data);
		if ($wrap) {
			$data = the_cleaners_remove_wrapper ($data);
		}
		return $data;
	}
}

function the_cleaners_wrapper ($data) {
	return '<xt:tpl>' . $data . '</xt:tpl>';
}

function the_cleaners_remove_wrapper ($data) {
	return str_replace (
		array ('<xt:tpl>', '</xt:tpl>'),
		array ('', ''),
		$data
	);
}

function the_cleaners_strip_tidy_output ($data) {
	$one = strpos ($data, '<body>') + 6;
	$two = strpos ($data, '</body>') - $one;
	return substr ($data, $one, $two);
}

?>