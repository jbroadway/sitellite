<?php

unset ($parameters['param']);
unset ($parameters['files']);
unset ($parameters['error']);
unset ($parameters['_rewrite_sticky']);
unset ($parameters['page']);
unset ($parameters['mode']);

page_title (intl_get ('Anchors'));
echo template_simple ('anchors.spt', $parameters);

?>