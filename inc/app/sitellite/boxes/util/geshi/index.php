<?php

loader_import ('saf.Database.PropertySet');
loader_import ('sitellite.geshi.geshi');

$ps = new PropertySet ('mailform', 'snippet');

$geshi = new GeSHi ($ps->get ($parameters['code']), $parameters['lang']);
//$geshi->set_header_type (GESHI_HEADER_PRE_TABLE);
$geshi->enable_line_numbers (GESHI_NORMAL_LINE_NUMBERS);
$geshi->set_overall_class ('geshi');

echo $geshi->parse_code ();


?>
