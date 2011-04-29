--TEST--
saf.XML.XT
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.XT');

// constructor method

$xt = new XT ('$path', '$prefix');

// getDoc() method

var_dump ($xt->getDoc ('$data'));

// path() method

var_dump ($xt->path ());

// ignoreUntilLevel() method

var_dump ($xt->ignoreUntilLevel ('$level'));

// validate() method

var_dump ($xt->validate ('$data'));

// fill() method

var_dump ($xt->fill ('$data', '$obj', '$carry'));

// bind() method

var_dump ($xt->bind ('$path', '$data'));

// bindAttr() method

var_dump ($xt->bindAttr ('$path', '$attr', '$value'));

// messy() method

var_dump ($xt->messy ('$data', '$obj', '$carry'));

// box() method

var_dump ($xt->box ('$name', '$parameters'));

// form() method

var_dump ($xt->form ('$name'));

// inline() method

var_dump ($xt->inline ('$data'));

// makeMethod() method

var_dump ($xt->makeMethod ('$name', '$type', '$level'));

// _output() method

var_dump ($xt->_output ('$str'));

// wrap() method

var_dump ($xt->wrap ('$str'));

// setVal() method

var_dump ($xt->setVal ('$name', '$value'));

// getVal() method

var_dump ($xt->getVal ('$val', '$node', '$type', '$transform'));

// reverseEntities() method

var_dump ($xt->reverseEntities ('$data'));

// convertEntities() method

var_dump ($xt->convertEntities ('$data'));

// _default() method

var_dump ($xt->_default ('$node'));

// _default_end() method

var_dump ($xt->_default_end ('$node'));

// _default_cdata() method

var_dump ($xt->_default_cdata ('$node'));

// _ch_handler() method

var_dump ($xt->_ch_handler ('$node'));

// _header_handler() method

var_dump ($xt->_header_handler ('$node'));

// _header_end() method

var_dump ($xt->_header_end ('$node'));

// makeToc() method

var_dump ($xt->makeToc ('$title'));

// _tpl() method

var_dump ($xt->_tpl ('$node'));

// _tpl_end() method

var_dump ($xt->_tpl_end ('$node'));

// _tpl_cdata() method

var_dump ($xt->_tpl_cdata ('$node'));

// _doctype() method

var_dump ($xt->_doctype ('$node'));

// _xmldecl() method

var_dump ($xt->_xmldecl ('$node'));

// _xmlstyle() method

var_dump ($xt->_xmlstyle ('$node'));

// _comment() method

var_dump ($xt->_comment ('$node'));

// _comment_end() method

var_dump ($xt->_comment_end ('$node'));

// _comment_cdata() method

var_dump ($xt->_comment_cdata ('$node'));

// _note() method

var_dump ($xt->_note ('$node'));

// _note_end() method

var_dump ($xt->_note_end ('$node'));

// _note_cdata() method

var_dump ($xt->_note_cdata ('$node'));

// _import() method

var_dump ($xt->_import ('$node'));

// _set_obj() method

var_dump ($xt->_set_obj ('$node'));

// _set() method

var_dump ($xt->_set ('$node'));

// _exec() method

var_dump ($xt->_exec ('$node'));

// _register() method

var_dump ($xt->_register ('$node'));

// _inc() method

var_dump ($xt->_inc ('$node'));

// _inc_end() method

var_dump ($xt->_inc_end ('$node'));

// _box() method

var_dump ($xt->_box ('$node'));

// _box_end() method

var_dump ($xt->_box_end ('$node'));

// _form() method

var_dump ($xt->_form ('$node'));

// _form_end() method

var_dump ($xt->_form_end ('$node'));

// _intl() method

var_dump ($xt->_intl ('$node'));

// _translate() method

var_dump ($xt->_translate ('$node'));

// _i18n() method

var_dump ($xt->_i18n ('$node'));

// _var() method

var_dump ($xt->_var ('$node'));

// _code() method

var_dump ($xt->_code ('$node'));

// _transform() method

var_dump ($xt->_transform ('$node'));

// _sql() method

var_dump ($xt->_sql ('$node'));

// _sql_end() method

var_dump ($xt->_sql_end ('$node'));

// _bind() method

var_dump ($xt->_bind ('$node'));

// _sub() method

var_dump ($xt->_sub ('$node'));

// _sub_end() method

var_dump ($xt->_sub_end ('$node'));

// _else() method

var_dump ($xt->_else ('$node'));

// _else_end() method

var_dump ($xt->_else_end ('$node'));

// _condition() method

var_dump ($xt->_condition ('$node'));

// _condition_end() method

var_dump ($xt->_condition_end ('$node'));

// _if() method

var_dump ($xt->_if ('$node'));

// _if_end() method

var_dump ($xt->_if_end ('$node'));

// _elseif() method

var_dump ($xt->_elseif ('$node'));

// _elseif_end() method

var_dump ($xt->_elseif_end ('$node'));

// _elsif() method

var_dump ($xt->_elsif ('$node'));

// _elsif_end() method

var_dump ($xt->_elsif_end ('$node'));

// _loop() method

var_dump ($xt->_loop ('$node'));

// _loop_end() method

var_dump ($xt->_loop_end ('$node'));

// _block() method

var_dump ($xt->_block ('$node'));

// _block_end() method

var_dump ($xt->_block_end ('$node'));

// _show() method

var_dump ($xt->_show ('$node'));

// _show_end() method

var_dump ($xt->_show_end ('$node'));

// _cache() method

var_dump ($xt->_cache ('$node'));

// _cache_end() method

var_dump ($xt->_cache_end ('$node'));

// template_xt() function

var_dump (template_xt ('$tpl', '$obj', '$carry'));

// template_messy() function

var_dump (template_messy ('$tpl', '$obj', '$carry'));

// template_validate() function

var_dump (template_validate ('$data'));

// template_wrap() function

var_dump (template_wrap ('$data'));

// template_error() function

var_dump (template_error ());

// template_err_line() function

var_dump (template_err_line ());

// template_err_colnum() function

var_dump (template_err_colnum ());

// template_convert_entities() function

var_dump (template_convert_entities ('$data'));

// template_toc() function

var_dump (template_toc ('$title'));

// template_bind() function

var_dump (template_bind ('$path', '$data'));

// template_bind_attr() function

var_dump (template_bind_attr ('$path', '$attr', '$value'));

// template_parse_body() function

var_dump (template_parse_body ('$body'));

?>
--EXPECT--
