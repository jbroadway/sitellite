<?php

loader_import('saf.Misc.RPC');

class Digger {
    function vote($score, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = session_username();
        if (! $user) {
            $user = 'anon';
        }
        
        if ($score == 'yes') {
            db_execute(
            	'UPDATE digger_linkstory SET score = score + 1 WHERE id = ?',
            	$id
            );
            
            db_execute(
            	'INSERT INTO digger_vote (id, story, score, user, ip, votetime) VALUES (null, ?, 1, ?, ?, NOW())',
            	$id, $user, $ip
            );
            
        } else {
            db_execute(
            	'UPDATE digger_linkstory SET score=score-1 WHERE id = ?',
            	$id
            );
            
            db_execute(
            	'INSERT INTO digger_vote (id, story, score, user, ip, votetime) VALUES (null, ?, -1, ?, ?, NOW())',
            	$id, $user, $ip
            );
            
            $score = db_shift('select score from digger_linkstory where id = ?', $id);
            if ($score <= appconf('ban_threshold')) {
                db_execute(
                	'update digger_linkstory set status = "disabled" where id = ?',
                	$id
                );
            }
        }
        
        return true;
    }
}

echo rpc_handle(new Digger(), $parameters);

exit;

?>