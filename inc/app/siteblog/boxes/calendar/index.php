<?php

global $cgi;

//form the query

$query = 'select id, created, subject from siteblog_post'; 

$constraints;

if (isset ($cgi->category)) {
    $constraints[] = 'category = ' . $cgi->category;
}

if (isset ($cgi->year) && isset ($cgi->month)) {
    
    $after = date ('Y-m-d', mktime (0,0,0, $cgi->month, 0, $cgi->year));
    
    $before = date ('Y-m-d', mktime (0,0,0, $cgi->month + 1, 1, $cgi->year));
    
} else {
    
    $year = date('Y');
    $month = date ('m');
    
    $after = date ('Y-m-d', mktime (0,0,0, $month, 0, $year));
    
    $before = date ('Y-m-d', mktime (0,0,0, $month + 1, 1, $year));
    
}

$constraints[] = 'created > "' . $after . '" and created < "' . $before . '"';

if (! empty ($constraints)) {
    
    $query .= ' where ';
    
    $total = count ($constraints);
    
    $sofar = 0;
    
    foreach ($constraints as $constraint) {
        
        $sofar++;
        
        $query .= $constraint;
        
        if ($sofar < $total) {
            $query .= ' and ';
        }
    }
}

$query .= ' order by created desc';

$res = db_fetch_array ($query);

//generate the calendar

loader_import ('siteblog.Filters');

loader_import ('saf.Date.Calendar.Mini');
$cal = new MiniCal ($parameters['minical']);

foreach ($res as $post) {
    
   
    list ($year, $month, $day) = explode ('-', $post->created);
    
    $cal->addLink ($day, '/index/siteblog-post-action/id.' . $post->id . '/title.' . siteblog_filter_link_title ($post->subject));
    
}

echo $cal->render ();



?>
