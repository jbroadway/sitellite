<?php

function siteforum_filter_highlight ($regs) {
	$text = $regs[1];

	$text = str_replace (
		array (
			'^^',
			'$$',
			'[[php::',
			'::php]]',
		),
		array (
			'<',
			'>',
			OPEN_TAG,
			CLOSE_TAG,
		),
		$text
	);

	ob_start ();
	highlight_string ($text);
	$text = ob_get_contents ();
	ob_end_clean ();

	return '<code>' . $text . '</code>';
}

function siteforum_filter_hidephp ($regs) {
	return '[[php::' . str_replace (array ('<', '>'), array ('^^', '$$'), $regs[1]) . '::php]]';
}

function siteforum_filter_htmlentities ($regs) {
	return '<code>' . htmlentities_compat ($regs[1]) . '</code>';
}

function siteforum_filter_outline ($regs) {
	if (! strstr ($regs[1], '&lt;?php')) {
		$regs[1] = '<pre>' . $regs[1] . '</pre>';
	}
	return '<div style="border: 1px solid #aaa; background-color: #fff; padding: 5px; margin: 10px; margin-top: 0px; margin-bottom: 0px;">' . str_replace ("\n", '', $regs[1]) . '</div>';
}

function siteforum_filter_attrs ($text) {
	return preg_replace (
		'/(script|on[a-z]+)=["\'](.*?)["\']/is',
		'',
		$text
	);
}

function siteforum_filter_strip_tags ($text) {
	// comment out php code for now...
	$text = preg_replace_callback (
		'|<\?php(.*?)\?' . '>|is',
		'siteforum_filter_hidephp',
		$text
	);

	// display html literally between <code></code> tags
	$text = preg_replace_callback (
		'|<code>(.*?)</code>|is',
		'siteforum_filter_htmlentities',
		$text
	);

	// now get rid of unwanted
	$text = strip_tags ($text, '<a><blockquote><i><u><b><strong><em><span><code><p><ul><ol><li><img><pre><address><h1><h2><h3><h4><h5><h6>');

	// now highlight your php code...
	$text = preg_replace_callback (
		'|(\[\[php::.*?::php\]\])|is',
		'siteforum_filter_highlight',
		$text
	);

	// turn <code></code> tags to <div></div>
	$text = preg_replace_callback (
		'|<code>(.*?)</code>|is',
		'siteforum_filter_outline',
		$text
	);

	// now go for attributes...
	$text = siteforum_filter_attrs ($text);

	return $text;
}

function siteforum_filter_body ($body) {
	if (appconf ('use_wysiwyg_editor')) {
		return siteforum_filter_strip_tags ($body);
	}
	return preg_replace (
                '|(http://[^\r\n\t ]+)|is',
                "'<a href=\"\\1\" target=\"_blank\">' . wordwrap ('\\1', 70, '<br />', 1) . '</a>'",
                str_replace (
                        NEWLINE,
                        '<br />' . NEWLINE,
                        siteforum_filter_strip_tags ($body)
                )
        );
}

function siteforum_filter_body_email ($body) {
	$body = str_replace (
    	array (
			'<b>',
			'<strong>',
			'<i>',
			'<em>',
			'</b>',
			'</strong>',
			'</i>',
			'</em>',
		),
        array (
			'*',
			'*',
			'_',
			'_',
			'*',
			'*',
			'_',
			'_',
		),
        strip_tags ($body, '<blockquote><i><u><b><strong><em>')
    );

	while (strstr ($body, '<blockquote>')) {
		$body = preg_replace_callback ('|<blockquote>(.*?)</blockquote>|is', 'siteforum_blockquotes', $body);
	}

	return $body;
}

function siteforum_blockquotes ($string) {
	return '> ' . str_replace (NEWLINE, NEWLINE . '> ', $string[1]);
}

function siteforum_filter_date ($date) {
	loader_import ('saf.Date');
	return Date::timestamp ($date, 'F j, Y - g:i A');
}

function siteforum_filter_shortdate ($date) {
	if (! $date) {
		return '';
	}
	loader_import ('saf.Date');
	return Date::timestamp ($date, 'M j - g:i A');
}

function siteforum_timezone ($offset) {
	$out = $offset[0];
	$offset = substr ($offset, 1);
	$h = floor ($offset / 3600);
	$m = floor (($offset % 3600) / 60);
	return $out . str_pad ($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad ($m, 2, '0', STR_PAD_LEFT);
}

function siteforum_filter_link_title ($t) {
	return strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$t
		)
	);
}

?>
