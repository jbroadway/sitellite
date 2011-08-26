<?php

loader_import('saf.Date');

function digger_filter_category($id)
{
    $name = db_shift('select category from digger_category where id = ?', $id);
    return '<a href="' . site_prefix() . '/index/digger-app/category.' . $id . '/name.' . $name . '">'
    . $name . '</a>';
}

function digger_filter_category_name($id)
{
    return db_shift('select category from digger_category where id = ?', $id);
}

function digger_filter_user($user)
{
    $pub = db_shift('select public from sitellite_user where username = ?', $user);
    if ($pub == 'yes') {
        return '<a href="' . site_prefix() . '/index/sitemember-profile-action/user.' . $user . '">' . $user . '</a>';
    }
    return $user;
}

function digger_filter_date($date)
{
    return Date::format($date, 'F j, Y g:i A');
}

function digger_filter_title($title)
{
    return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
}

function digger_filter_my_vote($v)
{
    if ($v > 0) {
        return '+' . $v;
    }
    return $v;
}

?>