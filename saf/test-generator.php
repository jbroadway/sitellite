<?php

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

include_once ('init.php');

$loader->import ('saf.File.Directory');

$skip = '/^lib\/(CVS|Ext|PEAR)/';

$files = Dir::find ('*.php', 'lib', 1);

$new_tests = array ();

foreach ($files as $k => $file) {
	if (preg_match ($skip, $file)) {
		unset ($files[$k]);
		continue;
	}
	$test_file = preg_replace ('/\.php$/', '', strtolower ($file));
	$test_file = explode ('/', $test_file);
	array_shift ($test_file);
	if ($test_file[0] == $test_file[1]) {
		array_shift ($test_file);
	}
	if ($test_file[count ($test_file) - 1] == $test_file[count ($test_file) - 2]) {
		array_pop ($test_file);
	}
	$test_file = join ('_', $test_file) . '.phpt';
	if (@file_exists ('tests/' . $test_file)) {
		unset ($files[$k]);
		continue;
	}

	// for testing:
	/*if ($test_file != 'file.phpt' && $test_file != 'functions.phpt') {
		unset ($files[$k]);
		continue;
	}*/

	$new_tests[$file] = array (
		'test_file' => $test_file,
		'loader_path' => $loader->translateRealPath (str_replace ('lib/', 'saf/', $file)),
		'classes' => array (),
		'functions' => array (),
	);

	$source = file_get_contents ($file);
	$tokens = token_get_all ($source);
	$class = false;
	$class_level = 0;
	$func = false;
	$level = 0;
	//info ($tokens);

	foreach ($tokens as $key => $token) {
		if (is_string ($token)) {
			// 1-char token
			if ($token == '{') {
				$level++;
			} elseif ($token == '}') {
				$level--;
			} elseif ($token == ')') {
				$func = false;
			}
		} else {
			list ($id, $text) = $token;

			switch ($id) {
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
					$level++;
					break;
				case T_CLASS:
					$class = $tokens[$key + 2][1];
					$class_level = $level;
					$new_tests[$file]['classes'][$class] = array ();
					break;
				case T_FUNCTION:
					// function or class method
					$func = $tokens[$key + 2][1];
					if (empty ($func)) {
						$func = $tokens[$key + 3][1];
					}
					if ($class && $level > $class_level) {
						$new_tests[$file]['classes'][$class][$func] = array ();
					} else {
						$new_tests[$file]['functions'][$func] = array ();
					}
					break;
				case T_VARIABLE:
					if ($func) {
						if ($class && $level > $class_level) {
							$new_tests[$file]['classes'][$class][$func][] = $text;
						} else {
							$new_tests[$file]['functions'][$func][] = $text;
						}
					}
			}
		}
	}
}

//info ($new_tests);
//echo '<pre>';

foreach ($new_tests as $file => $test) {
	$out = "--TEST--\n" . $test['loader_path'] . "\n--FILE--\n<?php\n\n// test setup\n\n// remove this when test is ready to be run\nreturn;\n\ninclude_once ('../init.php');\n\n// include library\n\nloader_import ('" . $test['loader_path'] . "');\n\n";
	foreach ($test['classes'] as $class => $methods) {
		if (isset ($methods[$class]) && count ($methods[$class]) > 0) {
			// constructor is defined and has parameters
			$out .= "// constructor method\n\n" . '$' . strtolower ($class) . ' = new ' . $class . ' (';
			$sep = '';
			foreach ($methods[$class] as $param) {
				$out .= $sep . "'" . $param . "'";
				$sep = ', ';
			}
			$out .= ");\n\n";
		} else {
			// no constructor defined
			$out .= "// constructor method\n\n" . '$' . strtolower ($class) . ' = new ' . $class . ";\n\n";
		}

		foreach ($methods as $method => $params) {
			if ($method == $class) {
				continue;
			}

			// generate test for this method
			$out .= '// ' . $method . "() method\n\nvar_dump (" . '$' . strtolower ($class) . '->' . $method . ' (';
			$sep = '';
			foreach ($params as $param) {
				$out .= $sep . "'" . $param . "'";
				$sep = ', ';
			}
			$out .= "));\n\n";
		}
	}

	foreach ($test['functions'] as $func => $params) {
		$out .= '// ' . $func . "() function\n\nvar_dump (" . $func . ' (';
		$sep = '';
		foreach ($params as $param) {
			$out .= $sep . "'" . $param . "'";
			$sep = ', ';
		}
		$out .= "));\n\n";
	}

	$out .= "?>\n--EXPECT--\n";
	file_put_contents ('tests/' . $test['test_file'], $out);
	//echo htmlspecialchars ($out);
}

echo 'Generated ' . count ($new_tests) . " new test files:\n";
foreach ($new_tests as $file => $test) {
	echo $test['loader_path'] . ' -> ' . $test['test_file'] . "\n";
}

?>