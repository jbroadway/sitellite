<?php

class ShoutboxAddForm extends MailForm {
    function ShoutboxAddForm () {
        parent::MailForm ();
        $this->parseSettings ('inc/app/shoutbox/forms/add/settings.php');
    }
    function onSubmit ($vals) {
        db_execute (
            'insert into shoutbox
                (id, name, url, ip_address, posted_on, message)
            values
                (null, ?, ?, ?, now(), ?)',
            $vals['name'],
            $vals['url'],
            $_SERVER['REMOTE_ADDR'],
            $vals['message']
        );

        header ('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

?>