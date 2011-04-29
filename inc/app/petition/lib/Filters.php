<?php

function petition_filter_signatures (&$obj) {
	return '<a href="' . site_prefix () . '/index/petition-signatures-action/id.' . $obj->id . '">' . db_shift ('select count(*) from petition_signature where petition_id = ?', $obj->id) . '</a>';
}

function petition_filter_province ($prov) {
	$list = appconf ('provinces');
	return $list[$prov];
}

function petition_filter_country ($c) {
	$list = appconf ('countries');
	return $list[$c];
}

?>