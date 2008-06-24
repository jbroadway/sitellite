<?php

function sitestreamer_flvheader () {
	static $once = true;
	if ($once) {
		if (strpos ($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			page_add_script (site_prefix () . '/inc/app/sitestreamer/lib/AC_RunActiveContent.js');
		} else {
			page_add_script (site_prefix () . '/inc/app/sitestreamer/lib/rac.js');
		}
	}
	$once = false;
}

function sitestreamer_flash ($file, $width, $height, $fg = '0x444444', $bg = '0xAAAAAA') {
	sitestreamer_flvheader ();

	$height += 40;

	return template_simple (
"<p align='center' class='sitestreamer-video'><object width='{width}' height='{height}' id='flvPlayer'>
 <param name='allowFullScreen' value='true'>
 <param name='movie' value='{site/prefix}/inc/app/sitestreamer/lib/player.swf?movie={site/prefix}/index/cms-filesystem-action{file}&fgcolor={fg}&bgcolor={bg}&volume=70'>
 <embed src='{site/prefix}/inc/app/sitestreamer/lib/player.swf?movie={site/prefix}/index/cms-filesystem-action{file}&fgcolor={fg}&bgcolor={bg}&volume=70' width='{width}' height='{height}' allowFullScreen='true' type='application/x-shockwave-flash'>
</object></p>",
		array (
			'file' => $file,
			'width' => $width,
			'height' => $height,
			'fg' => $fg,
			'bg' => $bg,
		)
	);
}

?>