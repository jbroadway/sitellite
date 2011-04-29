<?php

function siteblog_rule_year ($vals) {
    if ($vals['year'] == 0 && $vals['month'] != 0) {
        return false;
    } else {
        return true;
    }
}

?>
