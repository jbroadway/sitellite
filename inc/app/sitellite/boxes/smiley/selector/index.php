<?php
// smiley selector tool

// WORK AROUND: include config file (necesarry if this file is called from another .spt template)
include "inc/app/sitellite/conf/properties.php";

// Set the field (-> document.getElementById()) in which the smiley codes will come.
echo template_simple_register("field",$parameters["field"]);

echo template_simple("smiley/selector.spt",appconf("smileys"));

?>
