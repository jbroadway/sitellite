<?php

global $cgi;

$fail = false;

if (empty ($cgi->m) && ! isset ($cgi->m)) { $fail = true; }
if (empty ($cgi->r) && ! isset ($cgi->r)) { $fail = true; }
if (empty ($cgi->c) && ! isset ($cgi->c)) { $fail = true; }

if (! $fail) {
    
    //has this m/r/c combinations been counted?
    $counted = db_shift ('select count(id) from sitemailer2_link_tracker where campaign = ? and message = ? and recipient = ?', $cgi->c, $cgi->m, $cgi->r);

    if ($counted == "0") {
    
        if (! db_execute ('insert into sitemailer2_link_tracker (id, campaign, message, created, recipient) values (NULL, ?, ?, now(), ?)', $cgi->c, $cgi->m, $cgi->r)) {
            echo 'Failed to update tracker.';
            return;
        }
    }
}

$camp = db_single ('select * from sitemailer2_campaign where id = ?', $cgi->c);

if (is_int (strpos (strtolower ($camp->forward_url), 'http://'))) {
} else {
    $camp->forward_url = 'http://' . $camp->forward_url; 
}

header ('Location: ' . $camp->forward_url);
exit;

?>
