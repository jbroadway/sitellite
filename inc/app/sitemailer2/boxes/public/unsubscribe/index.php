<?php

global $cgi;

//info ($cgi);exit;

//make sure an all data needed is available
if (empty ($cgi->email)) {
    echo '<p>Invalid request: no email address found.</p>';
    return;
}

if (empty ($cgi->list)) {
    echo '<p>Invalid request: no newsletter found.</p>';
    return;
}

$cgi->newsletter = $cgi->list;

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

//see if recipient already exists
if ($recipient = db_shift_array ('select id from sitemailer2_recipient where email = ?',
                                 $cgi->email)) {
    $recipient = $recipient[0];
    
//recipient doesnt exist, create recipient and subscribe    
} else {
    echo '<p>That email address is not in our database, click back to try again</p>';
    return;
}

//if we don't have a recipient yet, quit!
if (empty ($recipient)) {
    echo '<p>Unable to unsubscribe, email address doesn\'t exists</p>';
    return;
}

$full_recipient = db_single ('select * from sitemailer2_recipient where id = ?', $recipient);  

if ($settings['unsubscribe_email_verification'] == 1) {
    
    echo '<p>Your subscription cancellation will not be complete until you receive a confirmation email.</p>';
    
    $data['firstname'] = $full_recipient->fname;
    $data['lastname'] = $full_recipient->lname;
    $data['email'] = $cgi->email;
    $data['organization'] = $full_recipient->organization;
    $data['website'] = $full_recipient->website;
    $created = db_shift ('select created from sitemailer2_recipient where id = ?', $recipient);
    $data['key'] = md5($recipient . $cgi->email . $created);
    $data['nl'] = $cgi->newsletter;
    
    //send out the request
    //import the mailer package
    loader_import ('ext.phpmailer');
    //initialize the mailer package
    $msg = new PHPMailer();
    $msg->Body = template_simple ('responses/email_unsub_confirmation.spt', $data);
    $msg->AltBody = template_simple ('responses/email_unsub_confirmation.spt', $data);
    $msg->AddAddress ($cgi->email, $cgi->fname . ' ' . $cgi->lname);
    $msg->From     = $settings['confirmation_email_domain'];
    $msg->FromName = $settings['confirmation_from'];
    $msg->Subject  = 'Subscription Cancellation'; 
    
    echo "<p>Sending message...</p>";
    
    if (! $msg->Send()) {
        echo '<p>Failed to send cancellation email. Are you sure the email address "' . $cgi->email . '" is valid? Click back to try again.</p>';
    } else {
        echo "<p>Message Sent Successfully!</p>";
    }
} else {
    
    //just unsubscribe
    $created = db_shift ('select created from sitemailer2_recipient where id = ?', $recipient);
                        
    header('Location: ' . site_prefix() . '/index/sitemailer2-public-unsubscribe-verify-action?email=' . $cgi->email . '&nl=' . $cgi->newsletter . '&key=' . md5($recipient . $cgi->email . $created));
    exit;
    
}

?>