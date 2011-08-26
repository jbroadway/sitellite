<?php

if ($box['context'] == 'action') {
	page_title (intl_get ('Newsletters'));
}

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

$lists = db_fetch_array ('select id, name from sitemailer2_newsletter where public = "yes" order by name asc');

if ($settings['subscriber_registration'] == 'none') {
    //stop, this should not be called
    echo '<p>Error, this should not be called.</p>';
    return;
    
} else if ($settings['subscriber_registration'] == 'email') {
    //only require the email, which we should have now
    echo template_simple ('publicemail.spt', $lists);
    
} else if ($settings['subscriber_registration'] == 'full') {
    //only require the email, but allow user to enter all info
    echo template_simple ('publicfull.spt', $lists);
    
} else if ($settings['subscriber_registration'] == 'req') {
    //require all settings
    echo template_simple ('publicreq.spt', $lists);
   
}


?>