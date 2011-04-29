<?php

// Prune activity log
if (appconf ('activity_log_items')) {
    db_execute ('DELETE FROM sitellite_log WHERE
            (TO_DAYS(NOW()) - TO_DAYS(ts)) > ?',
            appconf ('activity_log_items'));
}


// Prune versionning tables
if (appconf ('cms_revisions_items')) {

    $applications = parse_ini_file ('inc/conf/auth/applications/index.php');
    $names = array ();

    loader_import ('saf.File.Directory');

    $files = Dir::find ('*.php', 'inc/app/cms/conf/collections', false);

    foreach ($files as $file) {
        if (strstr ($file, '/.')) {
            continue;
        }
        $data = ini_parse ($file);
        if (! isset ($data['Collection']['visible']) || $data['Collection']['visible'] != false) {
            if (session_is_resource ($data['Collection']['name']) && ! session_allowed ($data['Collection']['name'], 'rw', 'resource')) {
                continue;
            }
            if (isset ($data['Collection']['app']) && isset ($applications[$data['Collection']['app']]) && ! $applications[$data['Collection']['app']]) {
                continue;
            }
            if (isset ($data['Collection']['is_versioned']) && $data['Collection']['is_versioned'] != false) {
                if (isset ($data['Collection']['key_field'])) {
                    $key = $data['Collection']['key_field'];
                }
                else {
                    $key = 'id';
                }
                $names[$data['Collection']['name']] = $key;
            }
        }
    }

    foreach ($names as $name=>$key) {
        $indexes = db_pairs ('SELECT ' . $key . ', COUNT(' . $key . ')
                FROM ' . $name . '_sv 
                WHERE `sv_current` = "no"
                GROUP BY ' . $key);
        foreach ($indexes as $id=>$n) {
            if (($l = ($n - appconf ('cms_revisions_items'))) >= 0) {
                db_execute ('DELETE FROM ' . $name . '_sv 
                    WHERE ' . $key . '=? 
		    AND sv_current = "no"
		    ORDER BY sv_revision
                    LIMIT ?', $id, $l+1);
            }
        }
    }
}


?>
