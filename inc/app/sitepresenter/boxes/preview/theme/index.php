<?php

global $cgi;

$pres = new StdClass;

$pres->id = 0;
$pres->title = 'Previewing Theme: ' . ucfirst ($cgi->theme);
$pres->ts = date ('YmdHis');
$pres->theme = $cgi->theme;
$pres->category = '';
$pres->keywords = '';
$pres->description = '';
$pres->cover = 'By Author Name<br />Company Name';
$pres->sitellite_status = 'approved';
$pres->sitellite_access = 'public';
$pres->sitellite_startdate = false;
$pres->sitellite_expirydate = false;
$pres->sitellite_owner = 'admin';
$pres->sitellite_team = 'none';

$pres->date = date ('Ymd');
$pres->fmdate = date ('F j, Y');

$pres->author = 'Author Name';
$pres->company = 'Company Name';

$pres->domain = 'example.com';

$pres->slides = array ();

$slide = new StdClass;
$slide->id = 0;
$slide->title = 'Slide One';
$slide->presentation = 0;
$slide->number = 1;
$slide->body = '<ul><li>Item One</li><li>Item Two</li><li>Item Three</li></ul>';
$pres->slides[] = $slide;

$slide = new StdClass;
$slide->id = 0;
$slide->title = 'Slide Two';
$slide->presentation = 0;
$slide->number = 2;
$slide->body = '<ul><li>Item One</li><li>Item Two</li><li>Item Three</li></ul>';
$pres->slides[] = $slide;

$slide = new StdClass;
$slide->id = 0;
$slide->title = 'Slide Three';
$slide->presentation = 0;
$slide->number = 3;
$slide->body = '<ul><li>Item One</li><li>Item Two</li><li>Item Three</li></ul>';
$pres->slides[] = $slide;

echo template_simple ('presentation.spt', $pres);

exit;

?>