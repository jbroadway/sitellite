<?php

while (ob_get_level ()) {
    ob_end_clean ();
}

ob_implicit_flush ();

//keep apache/sitellite out
if (php_sapi_name () != 'cli') {
    exit;
}
// END CLI KEEPOUT CHECKING

// mailcheck scheduler block
// Checks for new incoming mail.

global $conf;
global $cgi;

$args = $cgi->parseUri ();
array_shift ($args); 

$testing = false;
$quiet = false;

foreach ($args as $a) {
    if ($a == 'testing') {
        $testing = true;
        echo "Running in test mode...\n";
    } if ($a == 'quiet') {
        $quiet = true;
    }
}

if (! $quiet) echo "Loading Packages...\n";

loader_import ('sitemailer2.Bouncer');
loader_import ('saf.Mail.Pop3');
loader_import ('pear.Mail.mimeDecode');
loader_import ('cms.Workspace.Message');
loader_import ('saf.File.Store');

if ($testing) {

    loader_import ('saf.File.Directory');
    
    $folder = 'inc/app/sitemailer2/tests';
    
    foreach (Dir::find ('*.txt', $folder) as $file) {
        $messages[$file]['message'] = join ('', file ($file)); 
    }
    
} else {

    $settings = parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');
    
    if ($settings['email_id'] == "ENTER_YOUR_EMAIL_ID") {
        echo "You must configure your server settings under the settings tab in the sitemailer web interface!\n";
        exit;
    }
    
    $pop3 = new Pop3 ($settings['email_server'], $settings['email_port']);
    
    if (! $quiet) echo "Connecting to mail server...\n";
    
    if (! $pop3->connect ()) {
        echo $pop3->error . "\n";
        return;
    }
    
    if (! $quiet) echo "Authenicating...\n";
    
    if (! $pop3->authenticate ($settings['email_id'], $settings['email_password'])) {
        echo $pop3->error . "\n";
        return;
    }
    
    if (! $quiet) echo "Getting message list...\n";
    
    $messages = $pop3->listMessages ();
    
}

$wmsg = new WorkspaceMessage ();

$total = count ($messages);
$sofar = 0;

echo 'Found ' . $total . " messages.\n";

if ($total == 0) exit;

foreach ($messages as $number => $message) {
	
    $sofar++;
    
	if (! $quiet) echo 'Downloading message ' . $sofar . '/' . $total . "...\n";
    
    if (! $testing) {
	
        	set_time_limit (1000);
        
        $messages[$number]['message'] = $pop3->getMessage ($number);
        if ($messages[$number]['message'] === false) {
            echo $pop3->error . "\n";
        }
                
    }
    
    if (! $quiet) echo "Processing message...\n";
    
	// parse message and send to system
	$md = new Mail_mimeDecode ($messages[$number]['message']);
	$decoded = $md->decode (array (
		'include_bodies' => true,
		'decode_bodies' => true,
		'decode_headers' => true,
	));
	
	//parse the from line
	$from_email = getEmailFromAddress ($decoded->headers['from']);
	$fname = getFirstNameFromAddress ($decoded->headers['from']);
	$lname = getLastNameFromAddress ($decoded->headers['from']);
		
	/*if (strcasecmp ($from_email, $settings['email_address']) == 0) {
		$pop3->removeMessage ($number);
		continue;
	}*/	

	// determine priority
	if (preg_match ("/^[0-9]$/", $decoded->headers['x-priority'])) {
		$priority = $decoded->headers['x-priority'];
	} else {
		$priority = 3;
	}
	
    $body = '';
    
	//parse the body
	if (! empty ($decoded->body)) {
		if ($decoded->ctype_secondary == 'plain') {
			$body = $wmsg->formatBody ($decoded->body);
		} else {
			$body = $decoded->body;
		}
		$attachments = array ();
	} else {
		$body = '';
		$attachments = array ();
		foreach ($decoded->parts as $part) {
			if ($part->disposition == 'attachment' || $part->ctype_primary != 'text') {
	
				$a = array (
					'type' => 'document',
				);
				$a['name'] = $part->d_parameters['filename'];
				$a['body'] = $part->body;
				$a['mime'] = $part->ctype_primary . '/' . $part->ctype_secondary;
				$a['summary'] = '';
				$attachments[] = $a;
			} else {
				if ($part->ctype_secondary == 'plain') {
					$body .= $wmsg->formatBody ($part->body);
				} else {
					$body .= $part->body;
				}
			}
		}
	}

	//parse timestamp
	$ts;
	$unixtime = strtotime ($decoded->headers['date']);
	$ts = date ('Y-m-d H:i:s', $unixtime);

	//bounce detection begins
    if (! $quiet) echo "Bounce detection begins...\n";
    
    //rules to scan for
    $rules = array (
            'DSN',
            'Qmail',
            'Postfix',
            'Yahoo',
            'Caiwireless',
            'Exchange',
            'Exim',
            'Netscape',
            'Compuserve',
            'Microsoft',
            'GroupWise',
            'SMTP32',
            'SimpleMatch',
            'Yale',
            'LLNL',);
    
    $found_bounce = false;            
    $done_rules = false;
    foreach ($rules as $r) {
        
        $f = 'bouncer_' . strtolower ($r);
        
        //if true, then message is bounced
        if ($res = call_user_func ($f, $decoded)) {
            if (! $quiet) echo 'Bounced message detected from mailer ' . $r . "\n";

            if ($res === true) {
                if (! $quiet) echo "No recipient found, continuing bounce detection.\n";
                $found_bounce = true;
                continue;
            }
            
            if (! $quiet) echo 'Recipient(s) found: ' . implode (', ', $res) . "\n";
            
            unsub($res);
            count_bounce(count($res));
            
            $done_rules = true;
            break;
        } 
    }
    
    //count bounced messages even if a recipient wasn't found
    if ($found_bounce) {
        count_bounce(1);
    }
    
    if (! $done_rules && $testing) {
        echo "Message not a bounced message: " . $number . "\n";
    }
    
	//$pop3->removeMessage ($number);
}

if (! $testing) {
    $pop3->disconnect ();
}

echo "\n";

exit;

?>
