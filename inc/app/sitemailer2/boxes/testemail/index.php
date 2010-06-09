<?php

page_title ("Sending Test Message");

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

//import the mailer package
loader_import ('ext.phpmailer');

//initialize the mailer package
$msg = new PHPMailer();

//configure the mail transfer agent
$mta = appconf ('mta');
switch ($mta) {
    case 'smtp':
        $msg->IsSMTP ();
        foreach (appconf ('mta_smtp') as $k => $v) {
            $msg->{$k} = $v;
        }
        break;
    case 'sendmail':
        $msg->IsSendmail ();
        foreach (appconf ('mta_sendmail') as $k => $v) {
            $msg->{$k} = $v;
        }
        break;
    case 'qmail':
        $msg->IsQmail ();
        foreach (appconf ('mta_qmail') as $k => $v) {
            $msg->{$k} = $v;
        }
        break;
    case 'mail':
    default:
        $msg->IsMail ();
        foreach (appconf ('mta_mail') as $k => $v) {
            $msg->{$k} = $v;
        }
}

//compose the message
if (empty ($parameters['template'])) {
    $template = '{body}';
} else {
    $template = db_shift ('select body from sitemailer2_template where id = ?', $parameters['template']);
}

//generate the message

//we need the newsletter id
$newsletter = $parameters ['newsletter'];

//this needs a better solution, maybe keep track of the newsletter in the queued items
$newsletter = $newsletter[0];

//prepare the data
$data = array ('subject' => $parameters['subject'], 'email' => $parameters['test_email'],
               'fullname' => 'Test_Full_Name', 
               'firstname' => 'Test_First_Name', 
               'lastname' => 'Test_Last_Name',
               'organization' => 'Test_Organization', 
               'website' => 'Test_URL',
               'date' => date('Y-m-d'),
               'unsubscribe' => '[[[unsub]]]',
               'tracker' => '[[[tracker]]]');
               

$unsub = '<a href="http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-unsubscribe-action?email=' .
$parameters['test_email'] . '&list=' . $newsletter . '" >Unsubscribe</a>';

$tracker = '<img src="http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-tracker-action/' . $res->message . '.gif" />'; 
               
//make the html version of the message


//add body to template
$data['body'] = template_simple ($parameters['body'], $data);

$template = str_replace ('{body}', '{filter none}{body}{end filter}', $template); 

//generate the final html
$html = template_simple ($template, $data);

$html = str_replace ('[[[unsub]]]', $unsub, $html);
$html = str_replace ('[[[tracker]]]', $tracker, $html);

$pos = 0;
$last_pos = 0;

$offset = strlen ('[[[campaign_link=');

while ($pos = strpos ($html,  '[[[campaign_link=')) {
    
    //find the end
    $end = strpos ($html, ']]]', $pos);
    
    $camp_id = substr ($html, $pos + $offset, $end - $pos - $offset);
    
    $html = str_replace ('[[[campaign_link=' . $camp_id . ']]]', 'http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-link-action?c=' . $camp_id . '&m=' . $res->message, $html);
}

//create the text only version of the email
$html_body = preg_replace ('/<br ?\/?' . ">([^\n])/i", "<br />\n\$1", $html);

$text_body = preg_replace (
                 array ("/^[\r\n]+/", "/[\r\n]+$/", "/[\r\n][\r\n]+/", ),
                 array ("", "", "\n\n", ),
                 strip_tags ( str_replace (
                         array ('<hr />', '<hr>', '&nbsp;', '&copy;', '&lt;', '&gt;', ),
                         array ('--', '--', ' ', '(c)', '<', '>', ),
                         $html_body )
                         )
                 );

//ready to send off the message

//give $msg the content of the email
$msg->Body = $html_body;
$msg->AltBody = $text_body;
$msg->AddAddress ($parameters['test_email'], 'Test_First_Name Test_Last_Name');
$msg->From     = $parameters['from_email'];
$msg->FromName = $parameters['from_name'];
$msg->Subject = $parameters['subject'];

if ($msg->Send()) {
    echo "<p>Message Sent Successfully</p>";
} else {
    echo "<p>Message Failed to Send</p>";
}

//clean up
$msg->ClearAddresses();

echo ('<a href="javascript: window.close ()">Close Window</a>');

?>
