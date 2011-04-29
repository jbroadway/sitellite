<?php

global $cgi;

// they can only submit a new headline if they're loggin-in.
if (! (session_valid())) {
    // send them back to login
    header('Location: /index/sitemember-app');
    exit;
}

// if no category is selected, select category as step 1.
if (! isset($cgi->category)) {
    page_title(intl_get('Submit a Story'));
    $categories = array('' => '-- SELECT --');
    foreach(db_pairs('select * from digger_category order by category asc') as $k => $v) {
        $categories[$k] = $v;
    }
    echo template_simple('form_add_category.spt', array('categories' => $categories));
    return;
}

class DiggerAddForm extends MailForm {
    function DiggerAddForm()
    {
        parent::MailForm();
        
        $this->parseSettings('inc/app/digger/forms/add/settings.php');
        
        page_title(intl_get('Submit a Story'));
    }
    
    function onSubmit($vals)
    {
        db_execute('INSERT INTO digger_linkstory (link, user, posted_on, score, title, category, status, description) VALUES (?, ?, NOW(), 0, ?, ?, "enabled", ?)',
        $vals['link'],
        session_username(),
        $vals['title'],
        $vals['category'],
        $vals['description']
        );
        
        header('Location: /index/digger-app');
        exit;
    }
}

?>