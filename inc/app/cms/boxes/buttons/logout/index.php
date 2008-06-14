<?php

if (! session_admin ()) {
	return;
}

echo template_simple ('<div style="text-align: center; float: left; padding-left: 8px; padding-right: 8px">
<a href="{site/prefix}/index/sitellite-user-logout-action?goto=cms-app"><img src="{site/prefix}/inc/app/cms/pix/icons/logout.gif" border="0" alt="{intl Log Out}" title="{intl Log Out}" /><br />
<a href="{site/prefix}/index/sitellite-user-logout-action?goto=cms-app">{intl Log Out}</a>
</div>');

?>