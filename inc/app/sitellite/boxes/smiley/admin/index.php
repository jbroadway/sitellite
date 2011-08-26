<?php

// PHP Smiley admin
$smileys = appconf("smileys");

echo template_simple("smiley/all.spt",$smileys);

?>
