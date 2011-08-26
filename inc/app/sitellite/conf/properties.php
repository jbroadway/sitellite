<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

// Set this to false if you do not want to use a WYSIWYG editor for editing
// posts in your forms.
appconf_set ('use_wysiwyg_editor', true);

// all smileys..
$smileys = array (
    "8-)" =>  "cool.png",
    ":cry:" => "cry.png",
    ":oops:" => "embarassed.png",
    ":-!" => "foot-in-mouth.png",
    ":(" => "frown.png",
    ":angel:" => "innocent.png",
    ":-X" => "kiss.png",
    ":lol:" => "laughing.png",
    ":-$" => "money-in-mouth.png",
    ":-#" => "sealed.png",
    ":)" => "smile.png",
    ":o" => "surprised.png",
    ":-P" => "tongue-out.png",
//    ":p" => "tongue-out.png",
    ":?" => "undecided.png",
    ";)" => "wink.png",
    ":O" => "yell.png"
);

appconf_set("smileys",$smileys);

// image tag, {filename} is filename of smiley image.
appconf_set("imgtag","<img src=\"http://".site_domain().site_prefix()."/inc/app/sitellite/pix/smiley/{filename}\" />");

?>