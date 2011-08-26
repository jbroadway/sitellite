<?php
// Smiley class and filter

// WORK AROUND: get the right config
include "inc/app/sitellite/conf/properties.php";

class Smiley {
    function replace_smileys($txt="",$ajax=false) {
     $smileys = appconf("smileys");
     $imgtag = appconf("imgtag");
     if($ajax) {
      // convert html image open/close tags (<>) to &open; and &close; (this will be restord in the javascript).
      $imgtag = str_replace( array ("<" , ">") , array("&open;" , "&close;"), $imgtag);
      // if people tried to post HTML in their message (with our used &open; and &close;)
      // this is changed "back" to < and > so htmlentities will make it safe in a later step
      $txt = str_replace ( array ("&open;" , "&close;" ) , array ("<" , ">") , $txt);
     }
     foreach($smileys as $code => $afb) {
      // replace smileys..
      $txt = str_replace(trim($code),str_replace("{filename}",$afb,$imgtag),$txt);
     }
     return $txt;
    }
}

// Filter to be used in spt files
function filter_smiley ($txt) {
    return Smiley::replace_smileys(htmlentities($txt));
}

?>