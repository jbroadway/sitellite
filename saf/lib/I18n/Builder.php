<?php

$GLOBALS['loader']->import ('saf.I18n');
$GLOBALS['loader']->import ('saf.Parser.Tokenizer');
$GLOBALS['loader']->import ('saf.Parser.Buffer');
$GLOBALS['loader']->import ('saf.File.Directory');

/**
 * @package I18n
 */
class I18nBuilder {
	var $buffer;
	var $error = false;
	var $root; // starting point
	var $except; // directories or files to skip
	var $extensions = array (
		'php' => '_php', // php script
		'tpl' => '_tpl', // xt template
		'spt' => '_spt', // simple template
		'collection' => '_collection', // collection file
		'settings' => '_settings', // form settings files
		'db' => '_db', // list of database tables/fields to be translated
		'config' => '_config',	// config.ini.php: to get app name
	);

	function I18nBuilder ($root, $except = false) {
		$this->root = $root;
		if (is_string ($except)) {
			$this->except[] = $except;
		} elseif (is_array ($except)) {
			$this->except = $except;
		}
		$this->buffer = new Buffer;
		global $intl;
		$this->intl =& $intl;
	}

	function build ($path = false) {
		if ($path === false) {
			$path = $this->root;
		}
		$dir = new Dir ($path);
		if (! $dir->handle) {
			$this->error = 'Failed to read directory: ' . $path;
			return false;
		}
		echo '<ul>';
		foreach ($dir->read_all () as $file) {
			if (strpos ($file, '.') === 0 || in_array ($file, array ('CVS', 'PEAR', 'Ext', 'pix', 'install', 'data', 'lang', 'modes.php', 'images.php', 'access.php')) || preg_match ('/\.(jpg|gif|png|css|js)$/i', $file)) {
				continue;
			}
			if (in_array ($path . '/' . $file, $this->except) || in_array ($file, $this->except)) {
				continue;
			}
			echo '<li>' . $path . '/' . $file . '</li>';
			if (@is_dir ($path . '/' . $file)) {
				if (! $this->build ($path . '/' . $file)) {
					$dir->close ();
					return false;
				}
			} elseif (
					@is_file ($path . '/' . $file) &&
					preg_match ('/\.(' . join ('|', array_keys ($this->extensions)) . ')$/i', $file, $regs)
			) {
				if ($file == 'settings.php' && strstr ($path, 'forms')) {
					$regs[1] = 'settings';
				} elseif ($file == 'translate.ini.php') {
					$regs[1] = 'db';
				} elseif (strstr ($path, 'conf/collections')) {
					$regs[1] = 'collection';
				} elseif ($file == 'config.ini.php') {
					$regs[1] = 'config';
				} elseif ($file == 'settings.ini.php') {
					$regs[1] = 'settings';
				}
				if (! $this->{$this->extensions[$regs[1]]} ($path . '/' . $file, $this->getContents ($path . '/' . $file))) {
					$dir->close ();
					return false;
				}
			} // else it's not a type of file we know how to parse
		}
		echo '</ul>';
		$dir->close ();
		return true;
	}

	function getList () {
		//info ($this->buffer, true);
		return $this->buffer->getAll ();
	}

	function getContents ($file) {
		if (@is_file ($file)) {
			return @join ('', @file ($file));
		}
		return $file;
	}

	function _config ($file, $data) {
		// get app name from config.ini.php
		if (! @file_exists ($file)) {
			return true;
		}

		$data = ini_parse ($file, false);

		ini_clear ();

		if (! is_array ($data)) {
			return true;
		}

		if (count ($data) == 0) {
			return true;
		}

		foreach ($data as $k => $v) {
			if ($k == 'app_name') {
				$this->buffer->set ($this->intl->serialize ($v), array (
					'string' => $v,
					'params' => false,
					'file' => $file,
					'line' => false,
					));
			}
		}
		return true;
	}

	function _collection ($file, $data) {
		if (! @file_exists ($file)) {
			return true;
		}

		$data = ini_parse ($file);

		ini_clear ();

		if (! is_array ($data)) {
			return true;
		}

		if (count ($data) == 0) {
			return true;
		}

		foreach ($data as $section => $values) {
			foreach ($values as $k => $v) {
				switch ($k) {
					case 'alt':
					case 'display':
					case 'key_field_name':
					case 'header':
					case 'singular':
					case 'title_field_name':
						$this->buffer->set ($this->intl->serialize ($v), array (
							'string' => $v,
							'params' => false,
							'file' => $file,
							'line' => false,
						));
						break;
					case 'values':
						if (substr ($v, 0, 5) == 'array') {
							eval ('$a = '.$v.";");
							if (is_array ($a)) {
								foreach ($a as $vv) {
									$this->buffer->set ($this->intl->serialize ($vv), array (
										'string' => $vv,
										'params' => false,
										'file' => $file,
										'line' => false,
							));

								}
							}
						}
						break;
				}
			}
		}

		return true;
	}

	function _settings ($file, $data) {
		if (! @file_exists ($file)) {
			return true;
		}

		ini_add_filter ('ini_filter_split_comma_single', array (
			'rule 0', 'rule 1', 'rule 2', 'rule 3', 'rule 4', 'rule 5', 'rule 6', 'rule 7', 'rule 8',
			'button 0', 'button 1', 'button 2', 'button 3', 'button 4', 'button 5', 'button 6', 'button 7', 'button 8',
		));

		$data = ini_parse ($file);

		ini_clear ();

		if (! is_array ($data)) {
			return true;
		}

		if (count ($data) == 0) {
			return true;
		}

		foreach ($data as $section => $values) {
			foreach ($values as $k => $v) {
				if ($section == 'Form' && ($k == 'title' || $k == 'message')) {
					$this->buffer->set ($this->intl->serialize ($v), array (
						'string' => $v,
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				} elseif ($k == 'alt' || $k == 'display_value' || $k == 'title' || $k == 'append' || $k == 'prepend' || $k == 'formhelp') {
					$this->buffer->set ($this->intl->serialize ($v), array (
						'string' => $v,
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				} elseif (strpos ($k, 'rule ') === 0 && is_array ($v)) {
					$this->buffer->set ($this->intl->serialize ($v[1]), array (
						'string' => $v[1],
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				} elseif (strpos ($k, 'button ') === 0) {
					if (is_array ($v)) {
						$v = $v[0];
					}
					$this->buffer->set ($this->intl->serialize ($v), array (
						'string' => $v,
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				} elseif (strpos ($v, 'eval:') === 0) {
					$this->_php ($file, OPEN_TAG . ' ' . substr ($v, 5) . ' ' . CLOSE_TAG);
				}
			}
		}

		return true;
	}

	function _db ($file, $data) {
		if (! @file_exists ($file)) {
			return true;
		}

		$data = parse_ini_file ($file);

		echo '<ul>';

		foreach ($data as $table => $fields) {
			echo '<li>table: ' . $table . '/' . $fields . '</li>';
			$fields = preg_split ('/, ?/', $fields);
			if (! is_array ($fields)) {
				$fields = array ($fields);
			}
			foreach ($fields as $field) {
				$res = db_shift_array (
					sprintf (
						'select distinct %s from %s where %s is not null and %s != ""',
						$field, $table, $field, $field
					)
				);
				foreach ($res as $row) {
					$this->buffer->set ($this->intl->serialize ($row), array (
						'string' => $row,
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				}
			}
		}

		echo '</ul>';

		return true;
	}

	function _spt ($file, $data) {
		//echo 'Reading ' . $file . '<br />';

		global $simple;

		$delim_start = $simple->delim[$simple->use_delim][0];
		$literal_start = $simple->delim_literal[$simple->use_delim][0];
		$delim_end = $simple->delim[$simple->use_delim][1];
		$literal_end = $simple->delim_literal[$simple->use_delim][1];

		$tokens = preg_split ('/(' . $delim_start . '[\[\]\(\)a-zA-Z0-9\.,=<>\?&#$\'":;\!\=\/ _-]+' . $delim_end . ')/s', $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		foreach ($tokens as $tok) {$_tok = substr (
				$tok,
				strlen ($literal_start),
				- strlen ($literal_end)
			);
			if (
					! empty ($_tok) &&
					strpos ($tok, $literal_start) === 0 &&
					strrpos ($tok, $literal_end) === (strlen ($tok) - strlen ($literal_end))
			) {
				$_is_tag = true;
			} else {
				$_is_tag = false;
			}

			if (strpos ($_tok, 'intl ') === 0) {
				$value = substr ($_tok, 5);
				$this->buffer->set ($this->intl->serialize ($value), array (
					'string' => $value,
					'params' => false,
					'file' => $file,
					'line' => false,
				));
			}
		}

		return true;
	}

	// the purpose of the remaining methods is to parse file contents into
	// unnamed arrays in $buffer, each containing 'string', 'params', 'file',
	// and 'line' (if possible)

	function _tpl ($file, $data) {
		//echo 'Reading ' . $file . '<br />';

/*
		$p = xml_parser_create ();
		if (! $p) {
			$this->error = 'Failed to create an XML parser!';
			return false;
		}
		if (! xml_parser_set_option ($p, XML_OPTION_CASE_FOLDING, false))  {
			xml_parser_free ($p);
			$this->error = 'Failed to disable case folding!';
			return false;
		}

		if (xml_parse_into_struct ($p, $data, $vals, $tags)) {
			xml_parser_free ($p);
			foreach ($vals as $node) {
				if (($node['type'] == 'open' || $node['type'] == 'complete') && $node['tag'] == 'xt:intl') {
					//echo 'Found One: ' . $node['value'] . '<br />';
					$this->buffer->set ($this->intl->serialize ($node['value']), array (
						'string' => $node['value'],
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				}
			}
		} else {
			$ec = xml_get_error_code ($p);
			$this->error = xml_error_string ($ec) . ' (Code ' . $ec;
			$this->error .= ', Line ' . xml_get_current_line_number ($p);
			$this->error .= ', Column ' . xml_get_current_column_number ($p) . ')';
			xml_parser_free ($p);
			//return false;

*/
/*			// parse with saf.HTML.Messy, works fine, but probably not worth the overhead
			global $loader;
			$loader->import ('saf.HTML.Messy');
			$messy = new Messy;
			$vals = $messy->parse ($data);
			foreach ($vals as $node) {
				if (($node['type'] == 'open' || $node['type'] == 'complete') && $node['tag'] == 'xt:intl') {
					//echo 'Found One: ' . $node['value'] . '<br />';
					$this->buffer->append (false, array (
						'string' => $node['value'],
						'params' => false,
						'file' => $file,
						'line' => false,
					));
				}
			}
*/

			// parse via a regex, nice and simple
			preg_match_all ('/<xt:intl>(.*?)<\/xt:intl>/', $data, $keys, PREG_SET_ORDER);
			//info ($keys);
			foreach ($keys as $node) {
				//echo 'Found One: ' . $node[1] . '<br />';
				$this->buffer->set ($this->intl->serialize ($node[1]), array (
						'string' => $node[1],
						'params' => false,
						'file' => $file,
						'line' => false,
				));
			}

			preg_match_all ('/intl_get ?\(\'(.*)?\'\)/', $data, $keys, PREG_SET_ORDER);
			//info ($keys);
			foreach ($keys as $node) {
				//echo 'Found One: ' . $node[1] . '<br />';
				$this->buffer->set ($this->intl->serialize ($node[1]), array (
						'string' => $node[1],
						'params' => false,
						'file' => $file,
						'line' => false,
				));
			}

//		}

		return true;
	}

	function _php ($file, $data) {
		//echo 'Reading ' . $file . '<br />';

		if (! function_exists ('token_get_all')) {
			$this->error = 'Tokenizer PHP extension is not available.';
			return false;
		}

		$tokens = Tokenizer::normalize (token_get_all ($data));

		// compile array from first ( to next ) after $intl
		// or $GLOBALS['intl']
		$add = array ();
		$glob = false;
		$i = false;
		$a = false;
		$s = false;

		foreach ($tokens as $tok) {
			//echo token_name ($tok[0]) . ': ' . $tok[1] . BR;
			if ($tok[0] == T_VARIABLE && $tok[1] == '$intl') {
				// found intl
		//		echo 'Found one ' . $tok[1] . '<br />';
				$i = true;

			} elseif ($tok[0] == T_STRING && $tok[1] == 'intl_get') {
				// found intl_get
				$i = true;

			} elseif ($tok[0] == T_STRING && $tok[1] == 'template_simple') {
				// we found a template_simple function!
				$s = true;

			} elseif ($tok[0] == T_VARIABLE && $tok[1] == '$GLOBALS') {
				// found global
		//		echo 'Found one ' . $tok[1] . '<br />';
				$glob = true;
				continue;
		//	} elseif ($glob && $tok[0] == 0 && $tok[1] == '[') {
		//		$add[] = array ();
			} elseif ($globa && $tok[0] == 0 && $tok[1] == ']') {
				$glob = false;
			} elseif ($glob && $tok[0] == T_CONSTANT_ENCAPSED_STRING && $tok[1] == '\'intl\'') {
				$i = true;
				$glob = false;

			} elseif ($i && $tok[0] == 0 && $tok[1] == '(') {
				$add[] = array ('');
				$a = true;
			} elseif ($i && $tok[0] == 0 && ($tok[1] == ')' || $tok[1] == ';')) {
				$i = false;
				$a = false;
			} elseif ($i && $a && $tok[1] == ',') {
				$add[count ($add) - 1][] = '';
			} elseif ($i && $a && $tok[0] != T_WHITESPACE) {
				$add[count ($add) - 1][count ($add[count ($add) - 1]) - 1] .= $tok[1];

			// parse the content of template_simple function
			// like if it was a .spt file.
			} elseif ($s && $tok[0] == 0 && $tok[1] == '(') {
				$str = '';
			} else if ($s && $tok[0] == 0 && ($tok[1] == ')' || $tok[1] == ';')) {
				$s = false;
				$this->_spt($file, $str);
			} elseif ($s && $tok[0] != T_WHITESPACE) {
				$str .= $tok[1];
			}
		}

		foreach ($add as $item) {
			$string = array_shift ($item);
			if (strpos ($string, "'") === 0) {
				$string = ltrim (rtrim ($string, "'"), "'");
			} elseif (strpos ($string, '"') === 0) {
				$string = ltrim (rtrim ($string, '"'), '"');
			} else {
				continue;
			}
		//	if (strstr ($string, "\\'") || strstr ($string, '\\"')) {
		//		$string = stripslashes ($string);
		//	}
			if (count ($item) > 0) {
				$params = $item;
			} else {
				$params = false;
			}

			$string = stripslashes ($string);

			$this->buffer->set ($this->intl->serialize ($string), array (
				'string' => $string,
				'params' => $params,
				'file' => $file,
				'line' => false,
			));
		}

		return true;
	}
}

?>
