<?php

//echo '<embed src="' . site_prefix () . '/index/cms-filesystem-action?file=' . $parameters['file'] . '" />';

static $n = 0;
$n++;

echo template_simple ('<script language="javascript" src="{site/prefix}/inc/app/sitestreamer/lib/audio-player.js"></script>
<object type="application/x-shockwave-flash" data="{site/prefix}/inc/app/sitestreamer/lib/audio-player.swf" id="audioplayer{n}" height="24" width="290">
<param name="movie" value="{site/prefix}/inc/app/sitestreamer/lib/audio-player.swf" />
<param name="FlashVars" value="playerID={n}&amp;soundFile={site/prefix}/index/cms-filesystem-action/{file}" />
<param name="quality" value="high" />
<param name="menu" value="false" />
<param name="wmode" value="transparent" />
</object>', array (
	'n' => $n,
	'file' => $parameters['file'],
));

?>