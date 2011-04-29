<?php

if (! session_admin ()) {
	return;
}

echo template_simple ('<div style="text-align: center; float: left; padding-left: 8px; padding-right: 8px">
<a href="{site/prefix}/index/cms-app"><img src="{site/prefix}/inc/app/cms/pix/icons/home.gif" border="0" alt="{intl Home}" title="{intl Home}" /><br />
{intl Home}</a>
</div>');

?>