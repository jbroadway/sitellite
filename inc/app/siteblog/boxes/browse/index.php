<?php

header ('Location: ' . site_prefix () . '/index/siteblog-app');
exit;

global $cgi;

page_add_style ('/inc/app/siteblog/html/post.css');
loader_import ('siteblog.Filters');

if (session_admin ()) {
    echo template_simple ('buttons.spt', '');
}

if (! isset ($cgi->by)) {
	$cgi->by = 'date';
}

if ($cgi->by == 'date') {

    echo template_simple ('browsehead.spt', array ('title' => 'Browsing Posts by: Month'));
    
    $res = db_fetch_array ('select id, created from siteblog_post');
    
    //create an array containing years, with relevant months inside, with a list of post ids
    
    $data = array();
    
    foreach ($res as $r) {
        //get the year
        
        $fh = strpos ($r->created, '-');
        $y = substr ($r->created, 0, $fh);
        $sh = strpos ($r->created, '-', $fh+1);
        $m = substr ($r->created, $fh+1, 2);
        
        $data[$y][$m][] = $r->id;
    }
    
    foreach ($data as $k=>$year) {
        
        echo template_simple ('yearlink.spt', array ('year' => $k));
        
        foreach ($year as $j=>$month) {
            
            $display_month = '';
            
            switch ($j) {
                case 1: $display_month = 'January'; break;
                case 2: $display_month = 'February'; break;
                case 3: $display_month = 'March'; break;
                case 4: $display_month = 'April'; break;
                case 5: $display_month = 'May'; break;
                case 6: $display_month = 'June'; break;
                case 7: $display_month = 'July'; break;
                case 8: $display_month = 'August'; break;
                case 9: $display_month = 'September'; break;
                case 10: $display_month = 'October'; break;
                case 11: $display_month = 'November'; break;
                case 12: $display_month = 'December'; break;
                default: $j;
            }
            
            echo template_simple ('bydate.spt', array ('year' => $k, 
                                                       'monthnum' => $j,
                                                       'count' => count ($month),
                                                       'month' => $display_month));

        }
        echo "\n<br />\n";
    }
    
} elseif ($cgi->by == 'user') {
   
    echo template_simple ('browsehead.spt', array ('title' => 'Browsing Posts by: Author'));
    
    foreach (db_shift_array ('select distinct author from siteblog_post') as $a) {
        
        $count = db_shift ('select count(id) from siteblog_post where author = ?', $a);
        
        echo template_simple ('bylink.spt', array ('var' => 'author', 'value' => $a, 'display' => $a,'count' => $count));
        
    }
    
} elseif ($cgi->by == 'category') {
   
    echo template_simple ('browsehead.spt', array ('title' => 'Browsing Posts by: Category'));
    
    foreach (db_fetch_array ('select * from siteblog_category') as $a) {
        
        if ($a->title == 'All Blogs') {
            
            $count = db_shift ('select count(id) from siteblog_post', $a->id);
            
        } elseif ($a->title == 'Personal Blog') {
            
            $count = db_shift ('select count(id) from siteblog_post where author = ? and category = ?', session_username (), $a->id);
            
        } else {
        
            $count = db_shift ('select count(id) from siteblog_post where category = ?', $a->id);
            
        }
        
        echo template_simple ('bylink.spt', array ('var' => 'category', 'value' => $a->id, 'display' => $a->title, 'title' => $a->title, 'count' => $count));

    }
    
}

?>
