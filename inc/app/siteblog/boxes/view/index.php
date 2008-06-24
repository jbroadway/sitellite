<?php

header ('Location: ' . site_prefix () . '/index/siteblog-app');
exit;

global $cgi;

foreach ($parameters as $k=>$p) {
    $cgi->$k = $p;
}

loader_import ('cms.Versioning.Rex');
$rex = new Rex ('siteblog_post');
loader_import ('siteblog.Filters');

page_add_style (site_prefix () . '/inc/app/siteblog/html/post.css');

//if (! empty ($cgi->template)) {
//    $template = $cgi->template;
//} else {
    $template = 'posts.spt';
//}

if (! empty ($cgi->maxlen)) {
    $maxlen = $cgi->maxlen;
    
} else $maxlen = false;

$tproperties = db_fetch_array ('select * from siteblog_category');

foreach ($tproperties as $t) {
    $properties[$t->id] = array ('poster_visible' => $t->poster_visible, 
                                 'comments' => $t->comments); 
}

if (isset ($cgi->complex)) {
    
    if (isset ($cgi->head)) {
        echo template_simple ('browsehead.spt', array ('title' => 'Search Results'));
    }
    
    $query = 'select * from siteblog_post ';
    
    $query_condition = array ();
    
    if (isset ($cgi->author)) $query_condition[] = 'author = "' . $cgi->author . '"';
    if (isset ($cgi->year))   $query_condition[] = 'YEAR(created) = "' . $cgi->year . '"';
    if (isset ($cgi->month))  $query_condition[] = 'MONTH(created) = "' . $cgi->month . '"';
    
    if (isset ($cgi->category))  {
        
        $catname = db_shift ('select title from siteblog_category where id = ?', $cgi->category);
    
        if ($catname == 'All Blogs') {
            
        } elseif ($catname == 'Personal Blog') {
            
            $query_condition[] = 'author = "' . session_username () . '"';
            
        } else {
        
            $query_condition[] = 'category = ' . $cgi->category;
    
        }
    }
    
    if (! empty ($query_condition)) $query .= ' where ';
    
    foreach ($query_condition as $k=>$q) {
        
        if (count ($query_condition) - $k == 1) {//last condition
            $query .= $q;
        } else {
            $query .= $q . ' and '; 
        }
    }
    
    $query .= ' order by created desc ';

    if (isset ($cgi->limit)) $query .= ' limit ' . $cgi->limit;
    
    $res = db_fetch_array ($query );
    
    $size = count ($res);
    
    foreach ($res as $k=>$r) {
        
        if ($properties[$r->category]['comments'] == 'on')  {
            
           $res[$k]->comments = db_shift ('select count(id) from siteblog_comment where child_of_post = ?', $r->id);
           $res[$k]->comments_on = true;
        } else {
            $res[$k]->comments_on = false;
        }
        
        if ($properties[$r->category]['poster_visible'] == 'yes')  {
            $res[$k]->show_author = true;
        }
        
        if (is_int($maxlen)) {
            if (strlen($res[$k]->body) > $maxlen) {
                $res[$k]->body = substr ($res[$k]->body, 0, $maxlen) . '...';
            }
        }
    }

	$res->category_name = db_shift ('select title from siteblog_category where id = ?', $res->category);
    
    echo template_simple ($template, array ('post' => $res));
    
} elseif (! empty ($cgi->id)) { //single post to view

	$template = 'post.spt';
     
    if (isset ($cgi->head)) {
        echo template_simple ('browsehead.spt', array ('title' => 'Viewing Single Post'));
    }
    
    $post = $rex->getCurrent ($cgi->id);
    
    if ($properties[$post->category]['comments'] == 'on')  {
		$post->comments_on = true;
		$post->comment = db_fetch_array ('select * from siteblog_comment where child_of_post = ? ORDER BY date ASC', $cgi->id);
		$post->comments = count ($post->comment);
    } else {
        $post->comments_on = false;
    }

    if ($properties[$post->category]['poster_visible'] == 'yes')  {
        $post->show_author = true;
    }

	$post->category_name = db_shift ('select title from siteblog_category where id = ?', $post->category);
    
    $cgi->post = $cgi->id;

    page_title ($post->subject);

    echo template_simple ($template, $post);
    
    //if (count ($comments) > 0) { 
        //echo '<a name="siteblog-comments"></a><p><strong> Comments </strong></p>';
        //echo template_simple ('comment.spt', array ('comment' => $comments, 'id' => $cgi->id));
    //} else {
    //    echo '<a name="siteblog-comments"></a><p><strong> No Comments </strong></p>';
    //}
    
} else { //all other cases

    if (! isset ($cgi->category) && ! isset ($cgi->year)) {
        
        if (isset ($cgi->head)) {
            echo template_simple ('browsehead.spt', array ('title' => 'Viewing All Posts'));
        }
        
        $res = db_fetch_array ('select * from siteblog_post order by created desc');
        
    } elseif (isset ($cgi->category)) {
        
        $catname = db_shift ('select title from siteblog_category where id = ?', $cgi->category);
        
        if (isset ($cgi->head)) {
            echo template_simple ('browsehead.spt', array ('title' => 'Viewing Posts from Category: ' . $catname));
        }
        
        if ($catname == 'All Blogs') {
            
            $query = 'select * from siteblog_post';
            
        } elseif ($catname == 'Personal Blog') {
            
            $query = 'select * from siteblog_post where author = "' . session_username () . '" and category = ' . $a->id;
            
        } else {
        
            $query = 'select * from siteblog_post where category = ' . $cgi->category;
    
        }
        
        $query .= ' ORDER BY created desc ';
        
        if (isset ($cgi->limit)) {
            $query .= ' limit ' . $cgi->limit; 
        }
        
        $res = db_fetch_array ($query);
        
    } elseif (isset ($cgi->year)) {
        
        $title = 'Viewing posts from ' . $cgi->year;
        $query = 'select * from siteblog_post where YEAR(created) = ' . $cgi->year;
        
        if (isset ($cgi->month)) {
            $title .= '/' . $cgi->month;
            $query .= ' and MONTH(created) = ' . $cgi->month; 
        }
        
         if (isset ($cgi->head)) {
            echo template_simple ('browsehead.spt', array ('title' => $title . $catname));
        }
        
        $query .= ' ORDER BY created desc ';
        
        if (isset ($cgi->limit)) {
            $query .= ' limit ' . $cgi->limit; 
        }
        
        
        $res = db_fetch_array ($query);
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

    }

	foreach (array_keys ($res) as $k) {
		$res[$k]->category_name = db_shift ('select title from siteblog_category where id = ?', $res[$k]->category);
	}

	page_title (appconf ('blog_name'));

    echo template_simple ($template, array ('post' => $res));
}

?>