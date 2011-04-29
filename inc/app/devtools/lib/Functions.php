<?php

function devtools_rule_app_unique ($vals) {
	if (@is_dir ('inc/app/' . $vals['appname'])) {
		return false;
	}
	return true;
}

function devtools_rule_tpl_unique ($vals) {
	if (@is_dir ('inc/html/' . $vals['setname'])) {
		return false;
	}
	return true;
}

?>