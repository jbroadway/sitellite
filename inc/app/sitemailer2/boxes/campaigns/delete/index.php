<?php

foreach ($parameters['delete'] as $camp) {
    
    //echo '<p>uncomment ' . $camp . '</p>';
    db_execute ('delete from sitemailer2_campaign where id = ?', $camp);
    
}

header ('Location: ' . site_prefix () . '/index/sitemailer2-campaigns-action');
exit;
?>
