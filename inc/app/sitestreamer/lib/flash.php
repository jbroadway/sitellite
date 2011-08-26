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
	static $num = 0;
	$num++;
	if (@file_exists ('inc/app/sitestreamer/lib/swfobject.js')) {
		$image = str_replace ('.flv', '.jpg', $file);
		if (! @file_exists ('inc/data' . $image)) {
			$image = '';
		}
		page_add_script (site_prefix () . '/inc/app/sitestreamer/lib/swfobject.js');
		return template_simple (
			'<p align="center" class="sitestreamer-video" id="sitestreamer-video{num}"><a href="http://www.macromedia.com/go/getflashplayer">You need the Flash Player to watch this video.</a></p>
			<script type="text/javascript">
				var swfobj{num} = new SWFObject ("{site/prefix}/inc/app/sitestreamer/lib/player.swf", "sitestreamer-video", "{width}", "{height}", "9", "{bg}");
				swfobj{num}.addParam ("allowfullscreen","true");
				swfobj{num}.addParam ("flashvars", "file={site/prefix}/index/cms-filesystem-action{file}{if not empty (obj[image])}&image={site/prefix}/index/cms-filesystem-action{image}{end if}");
				swfobj{num}.write ("sitestreamer-video{num}");
			</script>',
			array (
				'file' => $file,
				'image' => $image,
				'width' => $width,
				'height' => $height,
				'fg' => $fg,
				'bg' => $bg,
				'num' => $num,
			)
		);
	}
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