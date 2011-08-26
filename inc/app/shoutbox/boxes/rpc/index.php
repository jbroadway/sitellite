<?php
// RPC for shoutbox

loader_import ('saf.Misc.RPC');
loader_import ('sitellite.smiley.Smiley');

class Shoutbox {
    function getlatest($lid) {
     // Receive latest messages and send them to rpc
     $res = db_fetch_array (
      'select * from shoutbox where id > '.$lid.';'
     );
     foreach($res as $k=>$v) {
      // replace smileys with AJAX -> true
      $res[$k]->message = Smiley::replace_smileys($v->message,true);
     }
     return $res;
    }

    function sendmessage($name,$message) {
     // User sends a new message to the shoutbox.
      if(empty($name) or empty($message)) { return false; }

      // here I would like to include MailForm verification..

      // insert query
      db_execute (
            'insert into shoutbox
                (id, name, url, ip_address, posted_on, message)
            values
                (null, ?, ?, ?, now(), ?)',
            $name,
            '',
            $_SERVER['REMOTE_ADDR'],
            $message
        );

        return true;

    }
}

echo rpc_handle (new Shoutbox (), $parameters);
exit;

?>