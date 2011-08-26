<?php

global $cgi;

if (empty ($cgi->email)) {
    show_error ('no email address specified, cannot continue');
    return;
}

if (empty ($cgi->nl)) {
    show_error ('no newsletter specified, cannot continue');
    return;
}

if (empty ($cgi->key)) {
    show_error ('no key specified, cannot continue');
    return;
}

//make sure the recipient exists
if ($recipient = db_shift_array ('select id from sitemailer2_recipient where email = ?',
                                 $cgi->email)) {
    $recipient = $recipient[0];
} else {
    show_error ('the email address to be unsubscribed doesn\'t exist');
    return;
}

//verify key

$created = db_shift ('select created from sitemailer2_recipient where id = ?', $recipient);

if ($cgi->key === md5($recipient . $cgi->email . $created)) {
    
    if (! db_execute ('update sitemailer2_recipient_in_newsletter set status="unsubscribed" where recipient = ? and newsletter = ?', $recipient, $cgi->nl)) {
        show_error ('failed to unsubscribe email address');
        return;
    }
    db_execute ('delete from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter = ?', $recipient, $cgi->nl);
    echo '<p>You are now successfully unsubscribed!</p>'; 
    
} else {
    show_error ('the key is incorrect');
    return;
}

function show_error ($str) {
    $str = trim ($str);
    echo '<p>Subscription failed: ' . $str . '. 
    </p><p>Visit <a href="' . site_prefix() . '/index/sitemailer2-public-action">here</a> to try again.</p>';
}

?>
