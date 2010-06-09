<?php

/**
 * @package siteconnector
 */
function siteconnector_to_xml ($struct, $level = 0) {
	if ($level == 0) {
		$o = '<struct>';
	} else {
		$o = '';
	}
	foreach ((array) $struct as $k => $v) {
		$numeric = false;
		if (is_numeric ($k)) {
			$numeric = true;
			$k = 'item num="' . $k . '"';
		}
		$o .= '<' . $k . '>';
		if (is_array ($v) || is_object ($v)) {
			$o .= siteconnector_to_xml ($v, $level + 1);
		} else {
			$o .= xmlentities ($v);
		}
		if ($numeric) {
			$o .= '</item>';
		} else {
			$o .= '</' . $k . '>';
		}
	}
	if ($level == 0) {
		$o .= '</struct>';
	}
	return $o;
}

/**
 * @package siteconnector
 */
function siteconnector_from_xml ($data) {
	$o = array ();
	$level = array ();
	$stmt = '';

	$xp = xml_parser_create_ns ('');
	xml_parser_set_option ($xp, XML_OPTION_CASE_FOLDING, false);
	$res = xml_parse_into_struct ($xp, $data, $vals, $index);
	if (! $res) {
		$code = xml_get_error_code ($xp);
		info (xml_error_string ($code), true);
		return array ();
	}
	xml_parser_free ($xp);

	foreach ($vals as $elem) {
		if ($elem['tag'] == 'item' && isset ($elem['attributes']['num'])) {
			$elem['tag'] = $elem['attributes']['num'];
		}
		if ($elem['type'] == 'open') {
			$level[$elem['level']] = $elem['tag'];
		}
		if ($elem['type'] == 'complete') {
			$start = 1;
			$stmt = '$o';
			while ($start < $elem['level']) {
				$stmt .= '[$level[' . $start . ']]';
				$start++;
			}
		}
		$stmt .= '[$elem[\'tag\']] = $elem[\'value\'];';
		@eval ($stmt);
	}

	if ($o['struct'] != NULL) {
		return array_shift ($o);
	} else {
		return array ();
	}
}

/**
 * @package siteconnector
 */
function siteconnector_get_raw_post_data () {
	$HTTP_RAW_POST_DATA = isset ($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	if (empty ($HTTP_RAW_POST_DATA)) {
		$fp = fopen ('php://input', 'r');
		if ($fp) {
			while (! feof ($fp)) {
				$line = fgets ($fp, 4096);
				$HTTP_RAW_POST_DATA .= $line;
			}
			fclose ($fp);
		}
	}
	return $HTTP_RAW_POST_DATA;
}

/**
 * @package siteconnector
 */
function siteconnector_wsdl ($file, $name, $ns = false) {
	$cache = 'inc/app/siteconnector/data/server/' . $name . '.wsdl';
	if (! preg_match ('/\.php$/', $file)) {
		global $loader;
		$file = $loader->translatePath ($file);
		if (! $file) {
			return false;
		}
	}
	if (@file_exists ($cache) && filemtime ($cache) > filemtime ($file)) {
		return @join ('', @file ($cache));
	}

	$wsdl = siteconnector_wsdl_render (
		siteconnector_wsdl_build (
			$file, $name, $ns
		)
	);

	if ((@file_exists ($cache) && @is_writeable ($cache)) || (! @file_exists ($cache) && @is_writeable (dirname ($cache)))) {
		loader_import ('saf.File');
		file_overwrite ($cache, $wsdl);
	}

	return $wsdl;
}

/**
 * @package siteconnector
 */
function siteconnector_wsdl_build ($file, $name, $ns = false) {
	if (! extension_loaded ('tokenizer')) {
		return false;
	}

	if (! preg_match ('/\.php$/', $file)) {
		global $loader;
		$file = $loader->translatePath ($file);
		if (! $file) {
			return false;
		}
	}

	if (! $ns) {
		$ns = site_url () . '/index/siteconnector-app/api.soap/service.' . $name;
	}

	$file_data = @join ('', @file ($file));
	$wsdl_struct = array (
		'ns' => $ns,
		'name' => $name,
		'types' => array (),
		'message' => array (),
		'portType' => array (),
		'binding' => array (),
	);

	if (! defined ('T_ML_COMMENT')) {
		define ('T_ML_COMMENT', T_COMMENT);
	} else {
		define ('T_DOC_COMMENT', T_ML_COMMENT);
	}

	$tokens = token_get_all ($file_data);

	$c = 0;

	foreach ($tokens as $token) {
		if (is_string ($token)) {
			continue;
		} else {
			list ($id, $text) = $token;
			switch ($id) {
				case T_COMMENT:
				case T_ML_COMMENT:
				case T_DOC_COMMENT:
					if (strpos ($text, '/**') === 0) {
						// new method
						if (preg_match_all ('/@([a-zA-Z0-9_-]+)[\t ]+([^\n\r]+)/s', $text, $regs, PREG_SET_ORDER)) {
							$info = array ();
							foreach ($regs as $reg) {
								if (isset ($info[$reg[1]])) {
									if (! is_array ($info[$reg[1]])) {
										$info[$reg[1]] = array ($info[$reg[1]]);
									}
									$info[$reg[1]][] = $reg[2];
								} else {
									$info[$reg[1]] = $reg[2];
								}
							}
							if (! isset ($info['access']) || $info['access'] != 'public') {
								break;
							}
						}
						$c = 2;
					}
					break;

				case T_FUNCTION:
					if ($c == 2) {
						$c--;
					} else {
						$c = 0;
					}
					break;

				case T_STRING:
					if ($c == 1) {
						$method = $text;
						$params = array ();
						if (isset ($info['return'])) {
							$return = array ('return' => $info['return']);
						} else {
							$return = array ();
						}
						$c = 0;

						if (! isset ($info['param'])) {
							$wsdl_struct = siteconnector_wsdl_add_method ($wsdl_struct, $method, $params, $return);
							$method = false;
						}
					}
					break;

				case T_VARIABLE:
					if ($method) {
						if (is_array ($info['param'])) {
							$params[$text] = array_shift ($info['param']);
							if (count ($info['param']) == 0) {
								$wsdl_struct = siteconnector_wsdl_add_method ($wsdl_struct, $method, $params, $return);
								$method = false;
							}
						} else {
							$params[$text] = $info['param'];
							$wsdl_struct = siteconnector_wsdl_add_method ($wsdl_struct, $method, $params, $return);
							$method = false;
						}
					}
					break;

				default:
					//echo token_name ($id) . ': ' . $text . NEWLINE;
					break;
			}
		}
	}

	return $wsdl_struct;
}

$GLOBALS['siteconnector_wsdl_type_list'] = array (
	'string' => 'xsd:string',
	'int' => 'xsd:int',
	'integer' => 'xsd:int',
	'bool' => 'xsd:boolean',
	'boolean' => 'xsd:boolean',
	'array' => 'soap:Array',
	'hash' => 'xsd:string',
	'struct' => 'xsd:string',
);

/**
 * @package siteconnector
 */
function siteconnector_wsdl_convert_type ($type) {
	global $siteconnector_wsdl_type_list;

	if (isset ($siteconnector_wsdl_type_list[$type])) {
		return $siteconnector_wsdl_type_list[$type];
	}

	return $type;
}

/**
 * Adds a custom type to the type list.
 *
 * @package siteconnector
 * @access	public
 * @param	array WSDL structure
 * @param	string
 * @param	mixed Type details
 * @return	array WSDL structure
 */
function siteconnector_wsdl_add_type ($struct, $name, $type, $info) {
	global $siteconnector_wsdl_type_list;

	$siteconnector_wsdl_type_list[$name] = $type;

	$struct['types'][$name] = array (
		'type' => $type,
		'info' => $info,
	);

	return $struct;
}

/**
 * @package siteconnector
 */
function siteconnector_wsdl_add_method ($struct, $name, $param, $return) {
	$struct['portType'][$name] = array (
		'param' => array (),
		'return' => array (),
	);

	if (count ($param) > 0) {
		foreach ($param as $k => $v) {
			$type = siteconnector_wsdl_convert_type ($v);
			$k = str_replace ('$', '', $k);
			$struct['portType'][$name]['param'][] = $k . 'Request';
			$struct['message'][$name . 'Request'][$k] = $type;
		}
	} else {
		$struct['portType'][$name]['param'][] = $name . 'Request';
		$struct['message'][$name . 'Request'] = array ();
	}

	if (count ($return) > 0) {
		foreach ($return as $k => $v) {
			$type = siteconnector_wsdl_convert_type ($v);
			$struct['portType'][$name]['return'][] = $k . 'Response';
			$struct['message'][$name . 'Response'][$k] = $type;
		}
	} else {
		$struct['portType'][$name]['return'][] = $name . 'Response';
		$struct['message'][$name . 'Response'] = array ();
	}

	$struct['binding'][$name] = array (
		'type' => $name,
		'action' => site_url () . '/index/siteconnector-app/api.soap/service.' . $struct['name'] . '#' . $name,
	);

	return $struct;
}

/**
 * @package siteconnector
 */
function siteconnector_wsdl_render ($struct) {
	$o = '<?xml version="1.0" encoding="UTF-8"?' . '>' . NEWLINEx2
		. '<definitions name="' . $struct['name'] . '" targetNamespace="' . $struct['ns'] . '"'
		. ' xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"'
		. ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
		. ' xmlns="http://schemas.xmlsoap.org/wsdl/"'
		. '>' . NEWLINEx2;

	foreach ($struct['types'] as $name => $type) {
	}

	foreach ($struct['message'] as $name => $msg) {
		$o .= TAB . '<message name="' . $name . '">' . NEWLINE;
		foreach ($msg as $k => $v) {
			$o .= TABx2 . '<part name="' . $k . '" type="' . $v . '" />' . NEWLINE;
		}
		$o .= TAB . '</message>' . NEWLINEx2;
	}

	$o .= TAB . '<portType name="' . $struct['name'] . '">' . NEWLINE;
	foreach ($struct['portType'] as $name => $pt) {
		$o .= TABx2 . '<operation name="' . $name . '"';
		if (is_array ($struct['message'][$name . 'Request'])) {
			$o .= ' parameterOrder="' . join (' ', array_keys ($struct['message'][$name . 'Request'])) . '"';
		}
		$o .= '>' . NEWLINE;
		foreach ($pt['param'] as $n => $t) {
			$o .= TABx3 . '<input name="' . $name . 'Request" message="' . $name . 'Request" />' . NEWLINE;
		}
		foreach ($pt['return'] as $n => $t) {
			$o .= TABx3 . '<output name="' . $name . 'Response" message="' . $name . 'Response" />' . NEWLINE;
		}
		$o .= TABx2 . '</operation>' . NEWLINEx2;
	}
	$o .= TAB . '</portType>' . NEWLINEx2;

	$o .= TAB . '<binding name="' . $struct['name'] . 'Binding" type="' . $struct['name'] . '">' . NEWLINE;
	$o .= TABx2 . '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http" />' . NEWLINE;
	foreach ($struct['binding'] as $name => $binding) {
		$o .= TABx2 . '<operation name="' . $name . '">' . NEWLINE;
		$o .= TABx3 . '<soap:operation soapAction="' . $binding['action'] . '" />' . NEWLINE;
		$o .= TABx3 . '<input>' . NEWLINE;
		$o .= TABx4 . '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $struct['ns'] . '" />' . NEWLINE;
		$o .= TABx3 . '</input>' . NEWLINE;
		$o .= TABx3 . '<output>' . NEWLINE;
		$o .= TABx4 . '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="' . $struct['ns'] . '" />' . NEWLINE;
		$o .= TABx3 . '</output>' . NEWLINE;
		$o .= TABx2 . '</operation>' . NEWLINEx2;
	}
	$o .= TAB . '</binding>' . NEWLINEx2;

	$o .= TAB . '<service name="' . $struct['name'] . '">' . NEWLINE;
	$o .= TABx2 . '<port name="' . $struct['name'] . '" binding="' . $struct['name'] . 'Binding">' . NEWLINE;
	$o .= TABx3 . '<soap:address location="' . site_url () . '/index/siteconnector-app/api.soap/service.' . $struct['name'] . '" />' . NEWLINE;
	$o .= TABx2 . '</port>' . NEWLINEx2;
	$o .= TAB . '</service>' . NEWLINEx2;

	$o .= '</definitions>';
	return $o;
}

?>