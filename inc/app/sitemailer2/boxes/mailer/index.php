<?php

while (ob_get_level ()) {
    ob_end_clean ();
}

ob_implicit_flush ();

//keep apache/sitellite out
if (php_sapi_name () != 'cli') {
    exit;
}

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

//import the mailer package
loader_import ('ext.phpmailer');

//initialize the mailer package
$msg = new PHPMailer ();

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

//we need to check if messages are done, do this while sleeping or every 1000 message sends
$checkfordone = 0;
$lastmessage;

//loop forever
while (1) {
    
    $checkdonecount++;
    
    if ($checkdonecount == 1000) {
        checkfordone ();
    }
    
    //get the next message
    $res = db_single ('select * from sitemailer2_q where next_attempt <= now() order by next_attempt asc limit 1');

    //info ($res, true);exit;

    
    //if there's no msgs sleep
    if (! $res) {
        //might as well check for messages that need to be marked done
        checkfordone ();
        echo "Sleeping...\n";
        sleep (10);
        continue;
    }
    
//compose the message

    if (! empty ($lastmessage)) {
        if ($res->message == $lastmessage->id) {
            $template = $lastmessage;
        } else {
            //grab the template
            $template = db_single ('select * from  sitemailer2_template t2, sitemailer2_message as
                                    t1 where t1.id = ? and t1.template = t2.id', $res->message);
        }
    } else {
        
        //grab the template
        $template = db_single ('select * from  sitemailer2_template t2, sitemailer2_message as t1 
                                where t1.id = ? and t1.template = t2.id', $res->message);
        
        $lastmessage = $template;   
    }
    
    //grab the recipient info
    $recipient = db_single ('select * from sitemailer2_recipient where id = ?', $res->recipient);
              
    //info ($template, true);exit;
    
    
	//generate the message
 
    //we need the newsletter id
    $newsletter = db_shift_array ('select newsletter from sitemailer2_message_newsletter where message = ?', $res->message);
    
    //this needs a better solution, maybe keep track of the newsletter in the queued items
    $newsletter = $newsletter[0];
    
    //info ($newsletter);exit;

    //prepare the data
    $data = array (
    	'subject' => $template->subject,
    	'email' => $recipient->email,
        'fullname' => $recipient->firstname . ' ' . $recipient->lastname, 
        'firstname' => $recipient->firstname, 
        'lastname' => $recipient->lastname,
        'organization' => $recipient->organization, 
        'website' => $recipient->website,
        'date' => date ('F j, Y'),
        'unsubscribe' => '[[[unsub]]]',
        'tracker' => '[[[tracker]]]',
    );
                   
	$unsub = '<a href="http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-unsubscribe-action?email=' .
    $recipient->email . '&list=' . $newsletter . '" >Unsubscribe</a>';
    
	$tracker = '<img src="http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-tracker-action/' . $res->message . '.gif" />'; 
                   
	//make the html version of the message
    
    //add body to template
    $data['body'] = template_simple ($template->mbody, $data);
    $template->body = str_replace ('{body}', '{filter none}{body}{end filter}', $template->body); 
    
    
    //info ($template);
    //info ($data, true);exit;
    
    //generate the final html
    $html = template_simple ($template->body, $data);
    
    $html = str_replace ('[[[unsub]]]', $unsub, $html);
    $html = str_replace ('[[[tracker]]]', $tracker, $html);
  
    //search for [[[campain_link=#]]] and replace it with a proper link
    
    $pos = 0;
    $last_pos = 0;
    
    $offset = strlen ('[[[campaign_link=');
    
    while ($pos = strpos ($html,  '[[[campaign_link=')) {
        
        //find the end
        $end = strpos ($html, ']]]', $pos);
        
        $camp_id = substr ($html, $pos + $offset, $end - $pos - $offset);
        
        $html = str_replace ('[[[campaign_link=' . $camp_id . ']]]', 'http://' . $settings['mailer_domain'] . '/index/sitemailer2-public-link-action?c=' . $camp_id . '&m=' . $res->message . '&r=' . $res->recipient, $html);
    }
    
    //info ($html);exit;
    
    $html = preg_replace ('/<br ?\/?' . ">([^\n])/i", "<br />\n\$1", $html);
    
    //create the text only version of the email
    $text_body = preg_replace (
                        array ("/^[\r\n]+/", "/[\r\n]+$/", "/[\r\n][\r\n]+/", ),
                        array ("", "", "\n\n", ),
                        strip_tags ( str_replace (
                                array ('<hr />', '<hr>', '&nbsp;', '&copy;', '&lt;', '&gt;', ),
                                array ('--', '--', ' ', '(c)', '<', '>', ),
                                $html )
                        )
                );
                
//ready to send off the message

    //give $msg the content of the email
    $msg->Body = $html;
    $msg->AltBody = $text_body;
    $msg->AddAddress ($recipient->email, $recipient->firstname . ' ' . $recipient->lastname);
    $msg->From     = $template->fromemail;
    $msg->FromName = $template->fromname;
    $msg->Subject = $template->subject;
    
    //info ($msg);exit;
    
    echo "Sending message " . $res->id . "\n";
    
    if ($msg->Send()) {
        //echo "Sent Message \n";
        db_execute ('delete from sitemailer2_q where id = ?', $res->id);
        db_execute ('update sitemailer2_message set numsent = numsent + 1 where id = ?', $res->message);
    } else {
        
        //check how many times message sending has been attempted
        if (($res->attempts + 1) >= appconf ('max_attempts')) {
            
            if (empty ($msg->ErrorInfo)){
                $msg->ErrorInfo = "Unknown Error. Check server configuration.";
            }
            
            db_execute ('delete from sitemailer2_q where id = ?', $res->id);
            
            if (! db_execute ('insert into sitemailer2_failed_q (id, recipient, message, attempts, created,
                         last_attempt, last_error) values (NULL, ?, ?, ?, ?, now(), ?)', $res->recipient, $res->message , $res->attempts + 1, $res->created, $msg->ErrorInfo)) {
                   echo "Failed to update failed q.\n";
             }
        } else {
            //update q'ed element, $res holds the info
            
            $next_attempt = date ('Y-m-d H:i:s', mktime (date('H'), date('i')+1, date('s'), date('m'), date('d'), date('Y')));
            
            if (! db_execute ('update sitemailer2_q set attempts=?, next_attempt = ? where id = ?', ($res->attempts+1), $next_attempt, $res->id)) {
                echo "Failed to increment attempts q.\n";
            }
            
            exit;
        }
    }
    
    //clean up
    $msg->ClearAddresses();
    
}

function checkfordone () {
    
    loader_import ('cms.Versioning.Rex');
    $rex = new Rex('sitemailer2_message');
    
    $res1 = db_fetch_array ('select * from sitemailer2_message where status = "running"');
        foreach ($res1 as $r1) {
            
            $q = db_shift ('select count(id) from sitemailer2_q where message = ?', $r1->id);
            $fq = db_shift ('select count(id) from sitemailer2_failed_q where message = ?',
                             $r1->id);
                 
            if ($q == 0) {
                
                //calc num sent
                if (! $fq) {
                    $numsent = $r1->numrec;
                } else {
                    $numsent = $r1->numrec - $fq;
                }
                
                $method = $rex->determineAction ($r1->id);
                $rex->{$method} ($r1->id, array ('status' => 'done',
                                                'numsent' => $numsent));
                              
                db_execute ('delete from sitemailer2_q_failed where message = ?', $r1->id);                                
          }
     }
     
     $checkdonecount=0;
}

?>