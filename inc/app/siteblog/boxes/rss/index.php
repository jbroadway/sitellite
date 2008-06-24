<?php

global $cgi;

foreach ($parameters as $k=>$p) {
    $cgi->$k = $p;
}

loader_import ('cms.Versioning.Rex');
$rex = new Rex ('siteblog_post');
loader_import ('siteblog.Filters');

page_add_style ('/inc/app/siteblog/html/post.css');

//if (! empty ($cgi->template)) {
//    $template = $cgi->template;
//} else {
    $template = 'post.spt';
//}

if (! empty ($cgi->maxlen)) {
    $maxlen = $cgi->maxlen;
    
} else $maxlen = false;

$tproperties = db_fetch_array ('select * from siteblog_category');

foreach ($tproperties as $t) {
    $properties[$t->id] = array ('poster_visible' => $t->poster_visible, 
                                 'comments' => $t->comments); 
}

if (isset ($cgi->category)) {
    
    $catname = db_shift ('select title from siteblog_category where id = ?', $cgi->category);
    
    if ($catname == 'All Blogs') {
        
        $query = 'select * from siteblog_post';
        
    } elseif ($catname == 'Personal Blog') {
        
        $query = 'select * from siteblog_post where author = "' . session_username () . '" and category = ' . $a->id;
        
    } else {
    
        $query = 'select * from siteblog_post where category = ' . $cgi->category;

    }
    
    $query .= ' ORDER BY created DESC ';
    
    if (isset ($cgi->limit)) {
        $query .= ' limit ' . $cgi->limit; 
    }
    
    $res = db_fetch_array ($query);
    
} else {
	$catname = '';
	$res = db_fetch_array (
		'select * from siteblog_post order by created desc limit 10'
	);
}

foreach ($res as $k=>$r) {
    
    if ($maxlen) {
        if (strlen($res[$k]->body) > $maxlen) {
            
            $res[$k]->body = substr ($res[$k]->body, 0, $maxlen) . '...';

        }
    }
    
    if ($properties[$r->category]['comments'] == 'on')  {
        
       $res[$k]->comments = db_shift ('select count(id) from siteblog_comment where child_of_post = ?', $r->id);
       $res[$k]->comments_on = true;
    } else {
        $res[$k]->comments_on = false;
    }
    
    if ($properties[$r->category]['poster_visible'] == 'yes')  {
        $res[$k]->show_author = true;
    }

	$res[$k]->category = db_shift ('select title from siteblog_category where id = ?', $r->category);
}

$res =& siteblog_translate ($res);

header ('Content-Type: text/xml');
echo template_simple (
    'rss_post.spt',
    array (
        'category' => $catname,
        'post' => $res,
        'category_id' => $cgi->category,
        'rss_date' => date ('Y-m-d\TH:i:s'),
    )
);

exit;
//echo template_simple ($template, array ('post' => $res));

?>
