<?php

page_title ('SiteMailer2 - Importer');

global $cgi;
global $params;

set_time_limit(0);

ob_implicit_flush();

if (isset ($cgi->submit)) {
    
    $data = (array)$cgi;
    
    unset ($data['files']);
    unset ($data['param']);
    unset ($data['error']);
    unset ($data['submit']);
    unset ($data['csvfile']);
    unset ($data['page']);
    unset ($data['mode']);
    unset ($data['rc']);
    
    $struct = array ();
    
    foreach ($data as $k=>$d) {
        //$k is of the form c# where # is the number of the column
        $col = substr ($k, 1);
        $struct[$d] = $col-1;
    }
    
    //info ($struct);exit;
    
    //at the very least the email field must be set
    if (! isset ($struct['email'])) {
        header ('Location: ' . site_prefix () . '/index/sitemailer2-verifycsv-action?error=noemail');
        exit;        
    }
    
    //make sure the file still exists
    if (! file_exists (site_docroot () . '/inc/app/sitemailer2/data/tmp/' . session_username ())) {
        echo '<pre>No file to import.</pre>';
        exit;
    }
    
    //now parse the csv file based on the col order
    $table = get_csv (site_docroot () . '/inc/app/sitemailer2/data/tmp/' . session_username (), 0);
    
    //there are many ways to get the name
    
    $fname; $lname; $email; $organization; $url;
    
    //if the user selected first name or last name, only use those fields 
    
    //option 0 -> omit names, try to get names from email address, see below for more options
    $name = 0;
    
    if (isset ($struct['firstname'])) $fname = $struct['firstname'];
    if (isset ($struct['lastname'])) $lname = $struct['lastname'];
    
    //use first name, last name as is
    $name = 1;
    
    //other name source are determined while looping through data 
     
    if (empty ($fname) && empty ($lname)) {
        if (isset ($struct['name'])) {
            //full name in one field
            $name = 2;
        } else if (! empty ($struct['lfname'])) {
            $name = 3;
        }   
    }
    
    $ids = array ();
    $user_count = 0;
    $dupes = 0;
    
    foreach ($table as $row) {
        
        $insfn = ""; $insln = ""; $org = ""; $url = ""; 
        
        $zero = 0;
        
        if (isset ($struct['organization'])) $org = $row[$struct['organization']];
        if (isset ($struct['www'])) $url = $row[$struct['www']];
        
        //first name, last name are ready
        if ($name == 1) {
            if (! empty ($fname) || $fname == 0) $insfn = $row[$fname];
            if (! empty ($lname) || $lname == 0) $insln = $row[$lname];
        // full name in one field
        } elseif ($name == 2) {
            $tname = explode (" ", $row[$struct['name']]);
            if (count ($tname) > 1) {
                $insfn = $tname[0];
                $insln = $tname[1]; 
            } else if (count ($tname) == 1) {
                $insfn = $tname[0];
            }
        //lastname, firstname
        } elseif ($name == 3) {
            $tname = preg_split ('/, ?/', $row[$struct['lfname']]);
            if (count ($tname) > 1) {
                $insfn = $tname[1];
                $insln = $tname[0]; 
            } else if (count ($tname) == 1) {
                $insfn = $tname[0];
            }
        //try to find names from email addresses
        } else {
            $insfn = getFirstNameFromAddress($row[$struct['email']]);
            $insln = getLastNameFromAddress($row[$struct['email']]);
        }
        
        //ready to insert new addresses now
        //echo 'Adding email: ' . getEmailFromAddress ($row[$struct['email']]) . '<br />';
        $email = getEmailFromAddress ($row[$struct['email']]);
        if (db_shift ('select count(*) from sitemailer2_recipient where email = ?', $email)) {
        	$dupes++;
        	continue;
        }
        
        if (empty ($insfn)) $insfn = '';
        if (empty ($insln)) $insln = '';
        if (empty ($org)) $org = '';
        if (empty ($url)) $url = '';
        
        if (! db_execute (
			'insert into sitemailer2_recipient
				(id, email, firstname, lastname, organization, website, created)
			values
				(null, ?, ?, ?, ?, ?, now())',
			$email,
			$insfn,
			$insln,
			$org,
			$url
        )) {
        	// unknown error
           echo db_error ();
        	continue;
        }

        $user_count++;
        
        //keep track of the id's added
        
        $ids[] = db_lastid(); 

        $lastid = db_lastid ();
        $groups = preg_split ('/, ?/', $cgi->rc);

		foreach ($groups as $group) {
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter
					(recipient, newsletter, status_change_time, status)
				values
					(?, ?, now(), "subscribed")',
				$lastid,
				$group
			);
		}
    }

	if (count ($dupes) == 1) {
		$dupes = ' with ' . $dupes . ' duplicate not imported';
	} elseif (count ($dupes) > 1) {
		$dupes = ' with ' . $dupes . ' duplicates not imported';
	} else {
		$dupes = '';
	}

	page_title ('SiteMailer 2 - Importer');    
    echo '<p>Successfully imported ' . $user_count . ' users' . $dupes . '.</p>';
    echo '<p><a href="' . site_prefix () . '/index/sitemailer2-subscribers-action">Return to subscriber list</a></p>';
    return;
}

if (! file_exists (site_docroot () . '/inc/app/sitemailer2/data/tmp/' . session_username ())) {
    echo '<pre>No file to import.</pre>';
    exit;
}

if (! empty ($cgi->error)) {
    echo '<pre>You must select one field as the email source.</pre>';
}

$table = get_csv (site_docroot () . '/inc/app/sitemailer2/data/tmp/' . session_username (), 3);

//how many columns are there?
$cols = 0;

if (! empty ($table)) {
    foreach($table[0] as $col) {
        $cols++;
    }
}

echo '<p>Please map the data to the correct fields below.</p>';

echo '<form action="' . site_prefix () . '/index/sitemailer2-verifycsv-action?submit&rc=' . $cgi->rc . '" method="post" name="verifycsv">';
$sels = '<select style="width:120px" name="c';
$sele ="\">
    <option value=\"ignore\">Ignore</option>
    <option value=\"email\">Email Address</option>
    <option value=\"name\">Full Name</option>
    <option value=\"firstname\">First Name</option>
    <option value=\"lastname\">Last Name</option>
    <option value=\"organization\">Organization</option>
    <option value=\"www\">Website</option>
</select>";       

echo "<table cellspacing='0' cellpadding='3'>\n";
echo '<tr>';

loader_import ('saf.Misc.Alt');

$alt = new Alt ('#fff', '#eee');

for ($i = 0; $i < $cols; $i++) {
    echo '<td style="padding: 3px; background: ' . $alt->next () . '">' . $sels . ($i + 1) . $sele . '</td>';    
}
echo '</tr>';

foreach ($table as $row) {
	$alt->reset ();
	echo "<tr>";
	foreach ($row as $data) {
		if (strlen ($data) > 20) {
			$data = substr ($data, 0, 17) . '...';
		}
		echo '<td style="background: '. $alt->next () . '">' . $data . '</td>';
	}
	echo "</tr>\n";
}
echo '<tr><td colspan="' . $cols . '">...</td></tr>';
echo "</table>\n";
echo '<input type="hidden" name="csvfile" value="' . $cgi->csvfile . '" />';
echo '<p><input type="submit" name="submit" value="Import" /></p>';
echo '</form>';


function get_csv($filename, $maxlines=0, $delim=',')
    {
       $row = 0;
       $dump = array();
       $f = fopen ($filename,"r");
       $size = filesize($filename)+1;
       while ($data = fgetcsv($f, $size, $delim)) {
           $dump[$row] = $data;
           $row++;
           if ($maxlines != 0 && $row >= $maxlines) break;
       }
       fclose ($f);
      
       return $dump;
} 

function getFirstNameFromAddress ($x) {
	
	//possible formats
	//1. Name <email>
	//2. email
	//3. "Name@domain" <email>
	//4. <email>
	
	//return empty string if no name
	
	$x = trim ($x);
	
	$s = strpos ($x, '<');
	$e = strpos ($x, '>');
	$q = strpos($x, "\"");
	
	$name = '';
	$fname = ''; //covers 2 and 4
	
	if ($e) { //check for 1 or 3
		if ($q) { //3
			$q2 = strpos ($x, "\"", $q+1);
			if ($q2) {
				$name = substr ($x, $q, $q2-$q1);
			} else { //erroneous address???, look for '<'
				$name = trim (substr ($x, $q, $q-$s));
			}
		} else { //1
			$name = trim (substr ($x, 0, $s));
		}
		//break up name if theres a space in it;
		$s = strpos ($name, ' ');
		if($s) {
			$fname = substr ($name, 0, $s);
		} else {
			$fname = $name;
		}
	}
	
	return $fname;
}

function getLastNameFromAddress ($x) {
	
	//possible formats
	//1. Name <email>
	//2. email
	//3. "Name@domain" <email>
	//4. <email>
	
	//return empty string if no name
	
	$x = trim ($x);
	
	$s = strpos ($x, '<');
	$e = strpos ($x, '>');
	$q = strpos($x, "\"");
	
	$name = '';
	$lname = ''; //covers 2 and 4
	
	if ($e) { //check for 1 or 3
		if ($q) { //3
			$q2 = strpos ($x, "\"", $q+1);
			if ($q2) {
				$name = substr ($x, $q, $q2-$q1);
			} else { //erroneous address???, look for '<'
				$name = trim (substr ($x, $q, $q-$s));
			}
		} else { //1
			$name = trim (substr ($x, 0, $s));
		}
		//break up name if theres a space in it;
		$s = strpos ($name, ' ');
		if($s) {
			$lname = substr ($name, $s, $e-$s);
		} else {
			$lname = '';
		}
	}
	
	return trim ($lname);
}

function getEmailFromAddress ($x) {
	
	//possible formats
	//1. Name <email>
	//2. email
	//3. "Name@domain" <email>
	//4. <email>
	
	//return empty string if no name
	
	$x = trim ($x);
	
	$s = strpos ($x, '<');
	$e = strpos ($x, '>');
		
	$addy = '';
	
	if ($e) { //1, 3 and 4
		$addy = substr ($x, $s+1, $e-$s-1);
	} else { //2
		$addy = $x;
	}
	
	return strtolower (trim ($addy));
}

?>

