<?php
/*
*   tickets resolved: #195 - javascript alert/confirm/prompt internationalization.
*/

loader_import ('multilingual.Filters');

define ('CMS_JS_CANCEL', '<script language="javascript" type="text/javascript">
<!--

function cms_cancel (f) {
	onbeforeunload_form_submitted = true;
	window.location.href = "' . site_prefix () . '/index/multilingual-app";
	return false;
}

// -->
</script>');

// Start: SEMIAS #195 javascript internalization
$intl_confirm = intl_get("Are you sure you want to delete this item?");

define ('HELPDOC_JS_DELETE_CONFIRM', '<script language="javascript">
<!--

function helpdoc_delete_confirm () {
	return confirm ("' . $intl_confirm . '");
}

// -->
</script>');
// END: SEMIAS

define ('HELPDOC_JS_SELECT_ALL', '<script language="javascript">
<!--

var cms_select_switch = false;

function helpdoc_select_all (f) {
	if (cms_select_switch == false) {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = true;
			cms_select_switch = true;
		}
	} else {
		for (i = 0; i < f.elements.length; i++) {
			f.elements[i].checked = false;
			cms_select_switch = false;
		}
	}
	return false;
}

// -->
</script>');

?>
