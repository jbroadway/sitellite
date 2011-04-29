<?php

global $cgi;

//info ($cgi);exit;

//make sure an all data needed is available
if (empty ($cgi->email)) {
    echo '<p>Invalid request: no email address found.</p>';
    return;
}


if (empty ($cgi->newsletter)) {
    if (empty ($cgi->list)) {
        echo '<p>Invalid request: no newsletter found.</p>';
        return;
    } else {
        $cgi->newsletter = $cgi->list;
    }
}

//set some default values if needed
if (empty ($cgi->fname)) { $cgi->fname = ""; };
if (empty ($cgi->lname)) { $cgi->lname = ""; };
if (empty ($cgi->organization)) { $cgi->organization = ""; };
if (empty ($cgi->website)) { $cgi->website = ""; };

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

//see if recipient already exists
//recipient exists, simply subscribe
if ($recipient = db_shift_array ('select id from sitemailer2_recipient where email = ?',
                                 $cgi->email)) {
    $recipient = $recipient[0];
    
//recipient doesnt exist, create recipient and subscribe    
} else {
    
    if (! db_execute ('insert into sitemailer2_recipient (id, email, firstname, lastname,
                       organization, website, created, status) values (NULL, ?, ?, ?, ?, ?,
                       now(), "unverified")', 
                       $cgi->email, $cgi->fname, $cgi->lname, $cgi->organization, $cgi->website)) {
        echo '<p>Error adding recipient.</p>';
        info ($cgi);
    }
    
    $recipient = db_lastid ();
}

//if we don't have a recipient yet, quit!
if (empty ($recipient)) {
    echo '<p>Unable to add recipient and recipient doesn\'t exists</p>';
}

//make sure the recipient is not already subscribed
if (db_shift ('select count(recipient) from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter = ? and status = "subscribed"', $recipient, $cgi->list)) {
    echo '<p>Your email address is already subscribed to this list.</p>';
    return;
}

//check if we only need to change the recipients status
if (db_shift ('select count(recipient) from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter = ? and status = "unsubscribed"', $recipient, $cgi->list)) {
        
} else {
    
    //add recipient to newsletter
    if (! db_execute ('insert into sitemailer2_recipient_in_newsletter (recipient, newsletter, status_change_time, status) values (?, ?, now(), "unsubscribed")', $recipient, $cgi->newsletter)) {
        echo '<p>Error adding recipient to newsletter</p>';
    }
}

if ($settings['subscriber_email_verification'] == 1) {
    
    echo '<p>Your subscription will not be complete until you receive a confirmation email.</p>';
    
    $data['firstname'] = $cgi->fname;
    $data['lastname'] = $cgi->lname;
    $data['email'] = $cgi->email;
    $data['organization'] = $cgi->organization;
    $data['website'] = $cgi->website;
    $created = db_shift ('select created from sitemailer2_recipient where id = ?', $recipient);
    $data['key'] = md5($recipient . $cgi->email . $created);
    $data['nl'] = $cgi->newsletter;
    
    //send out the request
    //import the mailer package
    loader_import ('ext.phpmailer');
    //initialize the mailer package
    $msg = new PHPMailer();
    $msg->Body = template_simple ('responses/email_confirmation.spt', $data);
    $msg->AltBody = template_simple ('responses/email_confirmation.spt', $data);
    $msg->AddAddress ($cgi->email, $cgi->fname . ' ' . $cgi->lname);
    $msg->From     = $settings['confirmation_email_domain'];
    $msg->FromName = $settings['confirmation_from'];
    $msg->Subject  = $settings['confirmation_subject']; 
    
    echo "<p>Sending message...</p>";
    
    if (! $msg->Send()) {
        echo '<p>Failed to send verification email. Are you sure the email address "' . $cgi->email . '" is valid? Click back to try again.</p>';
    } else {
        echo "<p>Message Sent Successfully!</p>";
    }
    
} else {
    
    $created = db_shift ('select created from sitemailer2_recipient where id = ?', $recipient);
    //just subscribe
                        
    header('Location: ' . site_prefix() . '/index/sitemailer2-public-subscribe-verify-action?email=' . $cgi->email . '&nl=' . $cgi->newsletter . '&key=' . md5($recipient . $cgi->email . $created));
    exit;
    
}



?>
