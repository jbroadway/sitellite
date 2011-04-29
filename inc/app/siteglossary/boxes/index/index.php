<?php

if ($box['context'] == 'action') {
	page_title (appconf ('title'));
}

$parm = $parameters[l];

$alphain = db_fetch_array ("select distinct lower(left(word,1)) as alpha from siteglossary_term order by word asc");
// filter db result to clean array
$indbarray = array();
foreach($alphain as $key => $value)
  $indbarray[] = strtoupper($value->alpha);

$result = array();

// loop alphabet and link terms with starting letter in database
foreach(range('A','Z') as $i){
  if(in_array($i,$indbarray))
	  if($parm == $i)
        $result[] = "<b><a href=\"siteglossary-app?l=$i\">[$i]</a></b>";
	  else
	    $result[] = "<a href=\"siteglossary-app?l=$i\">[$i]</a>";
  else
    $result[] = $i;
}

if(in_array($parm,range('A','Z')))
  $dba = db_fetch_array ("select * from siteglossary_term where (left(word,1) = '".$parm."') order by word asc");
elseif($parm == "all")
  $dba = db_fetch_array ("select * from siteglossary_term order by word asc");
else
{
  foreach(range('a','z') as $i){
    $dba = db_fetch_array ("select * from siteglossary_term where (left(word,1) = '".$i."') order by word asc");
    if(!empty($dba))
	break;
  }
}

echo template_simple (
    'glossary.spt',
    array(
        "result" => $result,
        "list" => $dba,
        "parm" => $parm)
    );

?>