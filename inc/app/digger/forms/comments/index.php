<?php

class DiggerCommentsForm extends MailForm {
    function DiggerCommentsForm()
    {
        parent::MailForm();
        
        $this->parseSettings('inc/app/digger/forms/comments/settings.php');
        $this->action = site_prefix() . '/index/digger-comments-action';
    }
    
    function show()
    {
        $out = '<h2>' . intl_get('Post a Comment') . '</h2>';
        $out .= parent::show();
        return $out;
    }
    
    function onSubmit($vals)
    {
        // process the form
        db_execute('INSERT INTO digger_comments (story, user, comment_date, comments) VALUES (?, ?, NOW(), ?)',
        $vals['id'], session_username(), $vals['comments']
        );
        
        $cid = db_lastid();
        
        // return back to main page
        header('Location: /index/digger-comments-action/id.' . $vals['id'] . '#digger-comment-' . $cid);
        exit;
    }
}

?>