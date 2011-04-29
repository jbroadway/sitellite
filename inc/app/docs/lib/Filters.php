<?php

function docs_filter_function ($func) {
	$out = highlight_string ("<?php\n" . $func . "\n?>", true);
	$out = str_replace ('&lt;?php<br />', '', $out);
	$out = str_replace ('<br /></span><span style="color: #0000BB">?&gt;</span>', '', $out);
	$out = str_replace ('?&gt;', '', $out);
	$out = str_replace ('<br />?&gt;</span>', '', $out);
	return $out;
}

function docs_filter_a_name ($n) {
	return str_replace ('_', '-', $n);
}

?>
