<?php

function sitetemplate_filter_template ($vals) {
	if (! template_validate ($vals['body'])) {
		return false;
	}
	return true;
}

function sitetemplate_rule_name ($vals) {
	if (@file_exists ('inc/html/' . $vals['set_name'] . '/' . $vals['output_mode'] . '.' . $vals['name'] . '.tpl')) {
		return false;
	}
	return true;
}

function sitetemplate_rule_name_css ($vals) {
	if (@file_exists ('inc/html/' . $vals['set_name'] . '/' . $vals['name'] . '.css')) {
		return false;
	}
	return true;
}

?>