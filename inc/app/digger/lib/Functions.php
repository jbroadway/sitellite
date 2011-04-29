<?php

function digger_has_voted($id)
{
    if (! session_valid()) {
        return false;
    }
    
    // can't vote on own stories
    if (db_shift('select count(*) from digger_linkstory where id = ? and user = ?', $id, session_username())) {
        return true;
    }
    
    // voted already
    if (db_shift('select count(*) from digger_vote where story = ? and user = ?', $id, session_username())) {
        return true;
    }
    
    return false;
}

function digger_timezone($offset)
{
    $out = $offset[0];
    $offset = substr($offset, 1);
    $h = floor($offset / 3600);
    $m = floor(($offset % 3600) / 60);
    return $out . str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
}

?>