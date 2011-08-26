<?php

// This code takes care of queueing recurring messages, and setting up following recurring messages

while (ob_get_level ()) {
    ob_end_clean ();
}

ob_implicit_flush ();

//keep apache/sitellite out
if (php_sapi_name () != 'cli') {
    exit;
}

while (1) {

    $reclist = db_shift_array ('select id from sitemailer2_message where recurring != "no" and status = "running" and next_recurrence <= now()');
    
    if (empty ($reclist)) {
        echo "Sleeping...\n";
        sleep (30);
        continue;
    }
    
    //$reclist contains all recurring messages that need to be sent out, now
    
    foreach ($reclist as $mid) {
        
        $message = db_single ('select * from sitemailer2_message where id = ?', $mid);
        //info ($message, true);
        
        //get a list of newsletters this message belongs to
        $newsletter = db_shift_array ('select newsletter from sitemailer2_message_newsletter where message = ?', $mid);
        
        $newsletter = implode (',', $newsletter);
        
        //info ($newsletter);exit;
        
        //get list of recipients
        $res = db_shift_array ('select distinct recipient from sitemailer2_recipient_in_newsletter 
                                            where newsletter in(' . $newsletter . ') and status="subscribed"');
                                            
        //add all emails in $res to the q with the neccesary info
        $added = 0;
        
        foreach ($res as $r) {
            db_execute ('insert into sitemailer2_q (id, recipient, message, attempts, created, last_attempt, last_error, next_attempt) 
                         values (null, ?, ?, 0, now(), "", "", ?)', $r, $mid, $message->next_recurrence);
            $added++;
        }        
   
        list ($date, $time) = explode (' ', $message->next_recurrence);
        
        list ($year, $month, $day) = explode ('-', $date);

        list ($hour, $minute, $second) = explode (':', $time);
        
        // 1. daily
        // 2. weekly
        // 3. twice-monthly
        // 4. monthly
        
        //set next send time
        if ($message->recurring == 'daily') {
            $day += 1;
        } else if ($message->recurring == 'weekly') {
            $day += 7;
        } else if ($message->recurring == 'twice-monthly') {
            $day += 15;
        } else if ($message->recurring == 'monthly') {
            $month += 1;
        } else {
            echo 'form error! invalid recurrence type:' . $message->recurring;exit;
        }
        
        $next_recurrence = date ('Y-m-d H:i:s', mktime ($hour, $minute, $second, $month, $day, $year));
        
       
        if (! db_execute ('update sitemailer2_message set next_recurrence=?, numrec=? where id=?', $next_recurrence, $message->numrec + $added, $mid)) {
            echo 'update of next_recurrence failed';
        }
    }
}

?>
