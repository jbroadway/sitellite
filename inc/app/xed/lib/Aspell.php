<?php

/* This package checks for the existence of the Pspell PHP extension, and if
 * not present, immitates the key functions of it by using the command-line
 * aspell function directly.
 *
 * Troubleshooting:
 *
 * 1. It returns no output (aka: PHP can't find aspell)
 *
 * There are 3 solutions here:
 * a) Change the aspell location via _pspell_set ('aspell', '/path/to/aspell');
 *    The disadvantage here is that this function is specific to this package,
 *    so if you have the Pspell extension installed, this won't work (however,
 *    you wouldn't be having this problem if that was the case ;)).
 * b) Change the path to aspell via putenv ('PATH=' . getenv ('PATH') . ':/aspell/path');
 *    This doesn't affect the Pspell extension, but may have safe_mode restictions.
 * c) Create a symbolic link to your aspell command somewhere in your path.  To
 *    find out your path, simply do a `echo getenv ('PATH');`.  Then you can say
 *    ln -s /real/path/to/aspell /usr/bin/aspell
 *
 */

if (defined ('PSPELL_NORMAL')) {
	return;
}

$GLOBALS['pspell_settings'] = array (
	'aspell' => 'aspell',
	'language' => false,
	'error' => false,
	'suggestions' => array (),
);

function _pspell_get ($name) {
	return $GLOBALS['pspell_settings'][$name];
}

function _pspell_set ($name, $value) {
	$old = _pspell_get ($name);
	$GLOBALS['pspell_settings'][$name] = $value;
	return $old;
}

function _pspell_error () {
	return _pspell_get ('error');
}

function _pspell_execute ($word) {
	$cmd = sprintf (
		'echo %s | %s -a --lang=%s --ignore-case',
		escapeshellarg ($word),
		_pspell_get ('aspell'),
		escapeshellarg (_pspell_get ('language'))
	);
	return shell_exec ($cmd);
}

// true: correct spelling
// false: incorrect spelling
// 
function _pspell_parse_response ($in, $word) {
	$orig = $in;
	if (strpos ($in, 'Error: ') === 0) {
		_pspell_set ('error', substr (trim ($in), 7));
		return -1;
	}
	if (strpos ($in, '@(#)') === 0) {
		$in = trim ($in);
		$in = preg_split ('(\r\n|\n\r|\r|\n)', $in);
		array_shift ($in);
		$in = array_shift ($in);
		if ($in == '*') { // correct
			return true;
		}
		if (strpos ($in, '#')) { // no suggestions
			return false;
		}
		// & means suggestions to follow
		$in = preg_replace ('/& ' . preg_quote ($word, '/') . ' [0-9]+ [0-9]+: /', '', $in);
		_pspell_set ('suggestions', preg_split ('/, ?/', $in));
		return false;
	}
	_pspell_set ('error', 'Unexpected response: ' . trim ($orig));
	return -1;
}

function pspell_new ($language) {
	_pspell_set ('language', $language);
	if (appconf ('pspell_location')) {
		_pspell_set ('aspell', appconf ('pspell_location'));
	}
	return true;
}

// bool
function pspell_check ($link, $word) {
	_pspell_set ('error', false);
	_pspell_set ('suggestions', array ());
	$out = _pspell_execute ($word);
	$res = _pspell_parse_response ($out, $word);
	if (is_bool ($res)) {
		return $res;
	}
	return false;
}

// array
function pspell_suggest ($link, $word) {
	$res = _pspell_get ('suggestions');
	if (count ($res) > 0) {
		return $res;
	}
	pspell_check ($link, $word);
	return _pspell_get ('suggestions');
}

/*
$link = pspell_new ('en');

$word = 'testt';
echo 'Word: ' . $word . '<br />';
if (pspell_check ($link, $word)) {
	echo 'Correct!';
} else {
	echo 'Wrong!  May I suggest:';
	echo '<ul><li>' . join ('</li><li>', pspell_suggest ($link, $word)) . '</li></ul>';
}

$word = 'speling';
echo 'Word: ' . $word . '<br />';
if (pspell_check ($link, $word)) {
	echo 'Correct!';
} else {
	echo 'Wrong!  May I suggest:';
	echo '<ul><li>' . join ('</li><li>', pspell_suggest ($link, $word)) . '</li></ul>';
}

$word = 'ascii';
echo 'Word: ' . $word . '<br />';
if (pspell_check ($link, $word)) {
	echo 'Correct!';
} else {
	echo 'Wrong!  May I suggest:';
	echo '<ul><li>' . join ('</li><li>', pspell_suggest ($link, $word)) . '</li></ul>';
}
*/

?>