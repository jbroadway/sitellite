<?php

page_title ('SiteMailer2 - Mass Unsubscriber');

global $cgi;
global $params;

set_time_limit(0);

ob_implicit_flush();

if (isset ($cgi->submit)) {
    
    $data = $cgi;
    
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
        header ('Location: ' . site_prefix () . '/index/sitemailer2-massunsub-action?error=noemail');
        exit;        
    }
    
    $file = site_docroot () . '/inc/app/sitemailer2/data/tmp/_' . session_username ();
    
    
    //make sure the file still exists
    if (! file_exists ($file)) {
        echo '<pre>No file to import.</pre>';
        exit;
    }
    
    //now parse the csv file based on the col order
    $table = get_csv ($file, 0);
    
    foreach ($table as $row) {
        
        $zero = 0;
        
        //ready to insert new addresses now
        //echo 'Adding email: ' . getEmailFromAddress ($row[$struct['email']]) . '<br />';
        $email = getEmailFromAddress ($row[$struct['email']]);
        
        //get all recipients with this email address
        
        $recipients = db_shift_array ('select id from sitemailer2_recipient where email = ?', $email);
        
        $recipients = implode ($recipients, ',');
        
        if (! db_execute (
            'update sitemailer2_recipient_in_newsletter set status_change_time = now(), status = "unsubscribed" where 
            recipient in('. $recipients . ') and newsletter in(' . $cgi->rc . ')')) {
                echo db_error();
                exit;
            }
        
		
    }

	page_title ('SiteMailer 2 - Importer');    
    echo '<p>Finished unsubscribing.</p>';
    echo '<p><a href="' . site_prefix () . '/index/sitemailer2-subscribers-action">Return to subscriber list</a></p>';
    return;
}

if (! file_exists (site_docroot () . '/inc/app/sitemailer2/data/tmp/_' . session_username ())) {
    echo '<pre>No file to import.</pre>';
    exit;
}

if (! empty ($cgi->error)) {
    echo '<pre>You must select one field as the email source.</pre>';
}

$table = get_csv (site_docroot () . '/inc/app/sitemailer2/data/tmp/_' . session_username (), 3);

//how many columns are there?
$cols = 0;

if (! empty ($table)) {
    foreach($table[0] as $col) {
        $cols++;
    }
}

echo '<p>Please map the data to the correct fields below.</p>';

echo '<form action="' . site_prefix () . '/index/sitemailer2-massunsub-action?submit&rc=' . $cgi->rc . '" method="post" name="massunsubcsv">';
$sels = '<select style="width:120px" name="c';
$sele ="\">
    <option value=\"ignore\">Ignore</option>
    <option value=\"email\">Email Address</option>
    <option value=\"name\">Full Name</option>
    <option value=\"firstname\">First Name</option>
    <option value=\"lastname\">Last Name</option>
    <option value=\"lfname\">Last Name, First Name</option>
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

