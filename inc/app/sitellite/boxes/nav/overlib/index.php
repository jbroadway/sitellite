<?php

loader_box ('sitellite/nav/init');

echo template_simple (
	'<div id="overDiv" style="position: absolute; width: 200px; visibility: hidden; z-index: 1000;"> </div>
	<script language="JavaScript" src="{site/prefix}/inc/app/sitellite/boxes/nav/overlib/overlib.js">
		<!-- overLIB (c) Erik Bosrup -->
	</script>
	<script language="JavaScript">
		ol_css = CSSCLASS;
		ol_fgclass = "overlib-foreground";
		ol_bgclass = "overlib-background";
		ol_textfontclass = "overlib-text";
		ol_captionfontclass = "overlib-caption";
	</script>' . NEWLINE
);

?>