<?php

if (! session_admin()) {
    header('Location: ' . site_prefix() . '/index/digger-app');
    exit;
}

class DiggerCommentsEditForm extends MailForm {
    function DiggerCommentsEditForm()
    {
        parent::MailForm();
        
        $this->parseSettings('inc/app/digger/forms/comments/edit/settings.php');
        
        page_title(intl_get('Editing Comment'));
        
        global $cgi;
        
        $comment = db_single('select * from digger_comments where id = ?', $cgi->id);
        
        $this->widgets['user']->setValue($comment->user);
        $this->widgets['comment_date']->setValue($comment->comment_date);
        $this->widgets['comments']->setValue($comment->comments);
        $this->widgets['story']->setValue($comment->story);
        
        page_add_script('
function digger_cancel (f) {
window.location.href = "' . site_prefix() . '/index/digger-comments-action/id.' . $cgi->story . '";
return false;
}
');
        
        $this->widgets['submit_button']->buttons[1]->extra = 'onclick="return digger_cancel (this.form)"';
    }
    
    function onSubmit($vals)
    {
        db_execute('update digger_comments set comments = ? where id = ?',
        $vals['comments'],
        $vals['id']
        );
        
        header('Location: ' . site_prefix() . '/index/digger-comments-action/id.' . $vals['story']);
        exit;
    }
}

?>