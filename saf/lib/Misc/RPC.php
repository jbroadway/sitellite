<?php

/**
 * Sets up the necessary javascript includes and such for using
 * the /js/rpc.js package.  See that package for more info.
 *
 * Usage:
 *
 * echo rpc_init ('javascript to handle server response here...');
 *
 * @param string
 * @return string
 * @package Misc
 */
function rpc_init ($handler = 'return false', $render = false) {
	if ($render) {
		$out = '<script language="javascript" type="text/javascript" src="' . site_prefix () . '/js/rpc.js"> </script>' . NEWLINE;
		$out .= '<script language="javascript" type="text/javascript">' . NEWLINE;
		$out .= '<!-- ' . NEWLINEx2;
		$out .= 'rpc_handler = new Function ("' . str_replace (
			array ("\n", "\r", '"'),
			array (' ', ' ', '\\"'),
			$handler
		) . '");' . NEWLINEx2;
		$out .= '// -->' . NEWLINE . '</script>' . NEWLINE;
		$out .= '<iframe id="rpc-caller" style="border: 0px none; width: 0px; height: 0px"> </iframe>';
		return $out;
	}
	page_add_script (site_prefix () . '/js/rpc.js');
	page_add_script ('
		rpc_handler = new Function ("' .str_replace (
			array ("\n", "\r", '"'),
			array (' ', ' ', '\\"'),
			$handler
		) . '");
	');
	return '<iframe id="rpc-caller" style="border: 0px none; width: 0px; height: 0px"> </iframe>';
}

/**
 * Creates a proper rpc server response out of the parameters passed to
 * this function (uses func_get_args(), so pass it whatever you want).
 *
 * Usage:
 *
 * echo rpc_response (true);
 * exit;
 *
 * @return string
 * @package Misc
 */
function rpc_response () {
	$args = func_get_args ();

	$out = "<html>\n\t<head>\n\t\t<meta http-equiv='pragma' content='no-cache' />\n\t</head>\n";
	$out .= "\t<body onload=\"window.parent.rpc_handler (";

	$op = '';
	foreach ($args as $arg) {
		$out .= $op . rpc_serialize ($arg);
		$op = ', ';
	}

	return $out . ")\">\n\t\t...\n\t</body>\n</html>";
}

/**
 * Serialize the specified PHP data into a JavaScript-compatible string.
 * Properly handles strings, numbers, boolean values, objects, and arrays,
 * but does not maintain key associations in arrays.
 *
 * @return string
 * @package Misc
 */
function rpc_serialize ($val) {
	if (is_numeric ($val)) {
		return $val;
	} elseif (is_bool ($val)) {
		return ($val) ? 'true' : 'false';
	} elseif (is_array ($val)) {
		$out = '[';
		$sep = '';
		foreach ($val as $k => $v) {
			$out .= $sep . rpc_serialize ($v);
			$sep = ', ';
		}
		$out .= ']';
		return $out;
	} elseif (is_object ($val)) {
		$out = '({ ';
		$sep = '';
		foreach (get_object_vars ($val) as $k => $v) {
			$out .= $sep . $k . ': ' . rpc_serialize ($v);
			$sep = ', ';
		}
		$out .= ' })';
		return $out;
	} else {
		return "'" . str_replace (
			array ("\n", "\r", '\'', '"', '>', '<'),
			array ('\\n', '\\r', '\\\'', '&quot;', '&gt;', '&lt;'),
			$val
		) . "'";
	}
}

/**
 * Takes an object and the parameter list from a box call, executes
 * the specified method (a 'method' entry in the parameter list)
 * with the rest of the values from the parameter list, and
 * returns a serialized JavaScript variable, suitable for passing
 * to the JavaScript eval() function.
 *
 * This function operates independently of the others (ie. you
 * do not need the calls to rpc_init() or rpc_response().  This
 * function is used in conjunction with the new XMLHttpRequest-based
 * RPC facilities in js/rpc.js.
 *
 * Note: The following are reserved parameter names:
 *
 * - error
 * - files
 * - method
 * - mode
 * - page
 * - param
 *
 * Usage:
 *
 * class Test {
 *   function hello ($name) {
 *     return 'hello ' . $name;
 *   }
 * }
 *
 * echo rpc_handle (new Test (), $parameters);
 * exit;
 *
 * @param object reference
 * @param array hash
 * @return string
 * @package Misc
 */
function rpc_handle (&$obj, $parameters) {
	// determine the method to call
	$method = $parameters['method'];
	if (! $method) {
		return rpc_serialize (false);
	}

	// remove unwanted parameters
	unset ($parameters['mode']);
	unset ($parameters['page']);
	unset ($parameters['error']);
	unset ($parameters['files']);
	unset ($parameters['param']);
	unset ($parameters['method']);
	unset ($parameters['_rewrite_sticky']);

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$res = rpc_serialize (
			call_user_func_array (
				array (&$obj, $method),
				$parameters
			)
		);
	} else {
		$res = rpc_serialize (
			call_user_func_array (
				array (&$obj, $method),
				$_POST
			)
		);
	}
	if (strstr ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && extension_loaded ('zlib')) {
		header ('Content-Encoding: gzip');
		$res = gzencode ($res, 9);
	}
	return $res;
}

?>