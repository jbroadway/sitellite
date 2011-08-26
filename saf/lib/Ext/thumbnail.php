<?php

global $loader;
include_once ($loader->paths['saf'] . '/Ext/upload/class.upload.php');

function makethumbnail($orig_file , $thumb_file , $max_width = 250 , $max_height = 250 , $extra = '') {

    // init upload class
    $filename = $orig_file;
    $handle = new Upload('./' . $filename );

    if(!$handle->uploaded) {
      echo intl_get("Something went wrong loading the image");
      echo "<br>".$handle->error;
      echo "<p><a href=".$return.">Click here to continue</a></p>";
      exit;
    }

    // Get directory
    $split = split("/",$filename);
    unset($split [ count($split) - 1 ] );
    $dir = implode("/",$split);

    // Get thumb dir
    $split2 = split("/",$thumb_file);
    $thumb_body = $split2 [ count($split2) - 1];
    unset($split2 [ count($split2) - 1] );
    $dir_thumb = implode("/",$split2);

    // set params
    $handle->file_safe_name = false;
    $handle->file_overwrite = true;
    $handle->file_auto_rename = false;

    // body: filename without extension
    $handle->file_new_name_body = substr($thumb_body, 0, strlen($thumb_body) - 4);

    $handle->image_resize         	= true;
    $handle->image_ratio_no_zoom_in = true;
    $handle->image_y                = $max_height;
    $handle->image_x                = $max_width;
    $handle->image_max_width = 1800;
    $handle->image_max_height = 1150;

    $handle->image_min_width = 25;
    $handle->file_max_size 			= '2097152'; // 2MB
    $handle->jpeg_quality = 95;
    $handle->file_new_name_ext = "jpg";
    $handle->image_background_color = '#FFFFFF'; // fill transparent color

    if(is_array($extra)) {
     foreach($extra as $k => $v) {
      $handle->$k = $v;
     }
    }

    $handle->Process('./' . $dir_thumb );

    // we check if everything went OK
    if (!$handle->processed) {
     // one error occured
     echo "<p>".intl_get("Something went wrong processing the image");
     echo "<br>".$handle->log;
     echo "<br>".$dir_thumb;
     echo "<br>".$handle->error."</p>";
     echo "<p><a href=".$return.">Click here to continue</a></p>";
     return false;
    }

    return true;
}
?> 