<?php

function monoslideshow_can_resize ($ext) {
	if ($ext != 'jpg' && $ext != 'jpeg') {
		return false;
	}
	if (! @is_writeable (site_docroot () . '/inc/app/monoslideshow/pix')) {
		return false;
	}
	if (! extension_loaded ('gd')) {
		return false;
	}
	if (@imagetypes () & IMG_JPG) {
		return true;
	}
	return false;
}

function monoslideshow_thumbnail ($file) {
	$full_path = $file;
	$info = pathinfo ($full_path);
	$ext = strtolower ($info['extension']);
	if (! monoslideshow_can_resize ($ext)) {
		return $file;
	}
	$save_to = md5 ($file) . '.' . $ext;
	$save_path = 'inc/app/monoslideshow/pix/';

	if (@file_exists ($save_path . $save_to) && @filemtime ($full_path) <= @filemtime ($save_path . $save_to)) {
		return site_prefix () . '/inc/app/monoslideshow/pix/' . $save_to;
	}

	
	list ($w, $h) = getimagesize ($full_path);
	$width = appconf ('thumbnail_width');
	$height = appconf ('thumbnail_height');
	if ($h > $w) {
		// cropping the height
		$hoffset = ($h - $w) / 2;
		$woffset = 0;
		$h -= $hoffset * 2;
	} else {
		// cropping the width
		$woffset = ($w - $h) / 2;
		$hoffset = 0;
		$w -= $woffset * 2;
	}
	$jpg = @imagecreatefromjpeg ($full_path);
	$new = @imagecreatetruecolor ($width, $height);
	@imagecopyresampled ($new, $jpg, 0, 0, $woffset, $hoffset, $width, $height, $w, $h);
	@imagejpeg ($new, 'inc/app/monoslideshow/pix/' . $save_to);
	@imagedestroy ($jpg);
	@imagedestroy ($new);
	return site_prefix () . '/inc/app/monoslideshow/pix/' . $save_to;
}

?>