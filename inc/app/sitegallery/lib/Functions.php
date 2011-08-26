<?php

function sitegallery_get_path ($file, $file_store = false) {
	if (! $file_store) {
		return site_docroot () . '/pix/' . $file;
	}
	return site_docroot () . '/inc/data/' . $file;
}

function sitegallery_url_path ($file, $file_store = false) {
	if (! $file_store) {
		return site_prefix () . '/pix/' . $file;
	}
	return site_prefix () . '/index/cms-filesystem-action?file=' . $file;
}

function sitegallery_can_resize ($ext) {
	if ($ext != 'jpg' && $ext != 'jpeg') {
		return false;
	}
	if (! @is_writeable (site_docroot () . '/inc/app/sitegallery/pix/thumbnails')) {
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

function sitegallery_get_thumbnail ($file, $file_store = false) {
	$full_path = sitegallery_get_path ($file, $file_store);
	$info = pathinfo ($full_path);
	$ext = strtolower ($info['extension']);
	if (! sitegallery_can_resize ($ext)) {
		return sitegallery_url_path ($file, $file_store);
	}
	$save_to = md5 ($file) . '.' . $ext;
	$save_path = 'inc/app/sitegallery/data/';

	if (@file_exists ($save_path . $save_to) && @filemtime ($full_path) <= @filemtime ($save_path . $save_to)) {
		return site_prefix () . '/inc/app/sitegallery/pix/thumbnails/' . $save_to;
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
	@imagejpeg ($new, 'inc/app/sitegallery/pix/thumbnails/' . $save_to);
	@imagedestroy ($jpg);
	@imagedestroy ($new);
	return site_prefix () . '/inc/app/sitegallery/pix/thumbnails/' . $save_to;
}

?>