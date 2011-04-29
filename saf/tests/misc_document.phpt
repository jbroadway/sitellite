--TEST--
saf.Misc.Document
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Document');

// constructor method

$document = new Document ('$data');

// set() method

var_dump ($document->set ('$data'));

// addHeader() method

var_dump ($document->addHeader ('$header'));

// sendHeaders() method

var_dump ($document->sendHeaders ());

// addMeta() method

var_dump ($document->addMeta ('$key', '$value', '$name'));

// makeMeta() method

var_dump ($document->makeMeta ());

// addScript() method

var_dump ($document->addScript ('$script'));

// addStyle() method

var_dump ($document->addStyle ('$style'));

// makeJavascript() method

var_dump ($document->makeJavascript ());

// addLink() method

var_dump ($document->addLink ('$rel', '$type', '$href', '$charset', '$hreflang', '$name'));

// compile() method

var_dump ($document->compile ());

// isExternal() method

var_dump ($document->isExternal ());

// useTemplate() method

var_dump ($document->useTemplate ());

// getSection() method

var_dump ($document->getSection ());

// getTitle() method

var_dump ($document->getTitle ('$page'));

// isParent() method

var_dump ($document->isParent ('$parent'));

// page_add_header() function

var_dump (page_add_header ('$header'));

// page_add_meta() function

var_dump (page_add_meta ('$key', '$value', '$name'));

// page_add_script() function

var_dump (page_add_script ('$script'));

// page_add_style() function

var_dump (page_add_style ('$style'));

// page_add_link() function

var_dump (page_add_link ('$rel', '$type', '$href', '$charset', '$hreflang', '$name'));

// page_id() function

var_dump (page_id ('$id'));

// page_title() function

var_dump (page_title ('$title'));

// page_head_title() function

var_dump (page_head_title ('$title'));

// page_nav_title() function

var_dump (page_nav_title ('$title'));

// page_description() function

var_dump (page_description ('$description'));

// page_keywords() function

var_dump (page_keywords ('$keywords'));

// page_below() function

var_dump (page_below ('$ref'));

// page_template() function

var_dump (page_template ('$value'));

// page_template_set() function

var_dump (page_template_set ('$value'));

// page_get_section() function

var_dump (page_get_section ());

// page_get_title() function

var_dump (page_get_title ('$page'));

// page_is_parent() function

var_dump (page_is_parent ('$parent'));

// page_onload() function

var_dump (page_onload ('$value'));

// page_onunload() function

var_dump (page_onunload ('$value'));

// page_onfocus() function

var_dump (page_onfocus ('$value'));

// page_onblur() function

var_dump (page_onblur ('$value'));

// page_onclick() function

var_dump (page_onclick ('$value'));

?>
--EXPECT--
