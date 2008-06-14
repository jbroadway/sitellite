<?php

$GLOBALS['formdata'] = array ();

$GLOBALS['formrules'] = array ();

/**
 * @package MailForm
 */

function formdata_set ($name, $list = array ()) {
	$GLOBALS['formdata'][$name] = $list;
}

function formrules_set ($name, $list = array ()) {
	$GLOBALS['formrules'][$name] = $list;
}

function formdata_get ($list, $makeAssoc = true) {
	if ($makeAssoc) {
		loader_import ('saf.MailForm');
		if (! isset ($GLOBALS['formdata'][$list])) {
			return array ();
		}
		return MailForm::makeAssoc ($GLOBALS['formdata'][$list]);
	} else {
		return $GLOBALS['formdata'][$list];
	}
}

function formrules_get ($list, $fieldName = false) {
	loader_import ('saf.MailForm.Rule');
	$rules = $GLOBALS['formrules'][$list];
	if (! is_array ($rules)) {
		return array ();
	}

	if (! $fieldName) {
		$fieldName = $list;
	}

	$out = array ();
	foreach ($rules as $rule) {
		$out[] = new MailFormRule ($rule[0], $fieldName, $rule[1]);
	}
	return $out;
}

?>