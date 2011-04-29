<?php

loader_import ('sitemailer2.Filters');

$camps = db_fetch_array ('select * from sitemailer2_campaign order by created desc');

$tracker = db_fetch_array ('select * from sitemailer2_link_tracker order by created desc');

foreach ($camps as $camp) {
    $temp[$camp->id] = $camp; 
}

$camps = $temp;

foreach ($tracker as $track) {
    $camps[$track->campaign]->clicks++;
}

if ($camps) {
foreach ($camps as $k=>$v) {
    if (empty ($camps[$k]->clicks)) {
        $camps[$k]->clicks = '0';
    }
}}

page_title ('Campaigns');

echo template_simple ('campaign.spt', array ('list' => $camps));

/*
echo '<br />';

foreach ($camps as $camp) {

    echo '<h3> ' . $camp->title . ' &nbsp<a title="Edit Campaign" alt="Edit Campaign" href="' . site_prefix () . '/index/sitemailer2-campaign-edit-form?id=' . $camp->id . '"><img src="' . site_prefix () . '/inc/app/cms/pix/icons/edit.gif" border="0" /></a></h3> ';
    echo '<ul >';
    echo '<li><strong>ID:</strong> ' . $camp->id . '</a></li>';
    echo '<li><strong>URL:</strong> <a href="' . $camp->forward_url . '">' . $camp->forward_url . '</a></li>';
    echo '<li><strong>Views:</strong></li> ';
    echo '<ul>';
    
    if (empty ($camp->messages)) {
        
        echo '<li>No confirmed views.</li>';
        
    } else {
       foreach ($camp->messages as $k=>$v) {
            
            echo '<li><strong><a href="' . site_prefix () . '/index/cms-edit-form?_collection=sitemailer2_message&_key=' . $k . '"> Message ' . $k . '</a>:</strong> ' . $v . '</li>';
            
        }
    }

    echo '</ul>';
    echo '</ul>';
    
}*/


?>
