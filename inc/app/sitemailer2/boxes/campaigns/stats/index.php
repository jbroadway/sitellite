<?php

loader_import ('sitemailer2.Filters');

//echo template_simple ('tabs.spt');

$id = $parameters['id'];

$res = db_single ('select * from sitemailer2_campaign where id = ?', $id);

$stats = db_fetch_array ('select * from sitemailer2_link_tracker where campaign = ?', $id);

page_title ('Campaign Statistics: ' . $res->title);

// Message, Newsletter, Date, Impressions, Impressions, Clicks, %

$messages = array ();

foreach ($stats as $stat) {
    $messages[$stat->message]['clicks']++;
}

$data = array ('forward_url' => $res->forward_url);

foreach ($messages as $k=>$message) {
    $res = db_single ('select * from sitemailer2_message where id = ?', $k);
    
    if ($res->subject) {
        $messages[$k]['subject'] = $res->subject;
    } else {
        $messages[$k]['subject'] = 'Nonexistent Message';
    }
    $messages[$k]['date'] = $res->date;
    if ($res->confirmed_views) {
        $messages[$k]['impressions'] = $res->confirmed_views;
    } else {
           $messages[$k]['impressions'] = '0';
    }
    $messages[$k]['message_id'] = $k;
    
    //get newsletter
    
    $res1 = db_shift ('select t2.name from sitemailer2_message_newsletter as t1, sitemailer2_newsletter as t2 where t1.message = ? and t2.id = t1.newsletter limit 1', $k);
    
    if ($res1) { 
        $messages[$k]['newsletter'] = $res1; 
    } else {
        $messages[$k]['newsletter'] = 'NA';
    }
        
    
    if ($res->confirmed_views != 0) {
        $messages[$k]['percent'] = round (($message['clicks'] / $res->confirmed_views) * 100, 1) . '%';
    } else {
        $messages[$k]['percent'] = 'NA';
    }
}

$data['list'] = $messages;

echo template_simple ('campstats.spt', $data);

//organize stats

?>
