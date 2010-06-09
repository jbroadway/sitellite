<?php

global $cgi;

//exected params: message
if (! isset ($cgi->message) || empty ($cgi->message)) {
    header ('Location: ' . site_prefix() . '/index/sitemailer2-app');
    exit;
}

$tpll = db_single ('select * from sitemailer2_template t2, sitemailer2_message as t1 
                   where t1.id = ? and t1.template = t2.id', $cgi->message);

$data = array ('subject' => 'Quisque quis dui quis massa eleifend dignissim', 
               'email' => 'email@domain.com',
               'recipient_name' => 'Jon Doe', 
               'recipient_firstname' => 'Jon', 
               'recipient_lastname' => 'Doe',
               'recipient_organization' => 'Doe Inc.', 
               'recipient_website' => 'www.jondoe.com');

$subject = template_simple ($tpll->subject, $data);

echo '<br /><p>Subject: ' . $subject . '</p>' . "\n";
               
$data['body'] = template_simple ($tpll->mbody, $data);

$tpll->body = str_replace ('{body}', '{filter none}{body}{end filter}', $tpll->body); 

echo template_simple ($tpll->body, $data);

?>
