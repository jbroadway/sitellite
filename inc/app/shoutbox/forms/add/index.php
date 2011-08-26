<?php
/*
class ShoutboxAdd2Form extends MailForm {
    function ShoutboxAdd2Form () {
        parent::MailForm ();
        $this->parseSettings ('inc/app/shoutbox/forms/add2/settings.php');
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
*/
$form = new MailForm;
$form->parseSettings("inc/app/shoutbox/forms/add/settings.php");
$form->attr ('onsubmit', 'shoutbox.sendmessage(); return false;');

echo $form->run();
?>