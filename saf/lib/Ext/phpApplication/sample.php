<?
/*
phpApplication v1.0 by nathan@0x00.org
http://0x00.org/php/phpApplication/
*/

	include("phpApplication.inc");
/* the following is what defines your application scope!  You can change sample to anything you want, but you will only be able to access variables within the same name */
	$APPLICATION=new phpApplication("sample");

	/* exclusive write lock */
	/* NOTE: If you write to a variable based on a previous value without obtaining an exclusive lock first, there is a chance that another process will write to the file and the data will not be consistent!!  You should always obtain an exclusive lock with something like a hit counter! */
	$APPLICATION->lock();
	$hitcount=$APPLICATION->get("hitcount");
	$APPLICATION->set("hitcount", $hitcount+1);
	$APPLICATION->unlock();
	
	/* shared read lock */
	$hitcount=$APPLICATION->get("hitcount");
	print "Current hitcount: $hitcount<br>\n";

	print "<br><br>";
	$setvars["A"]=0xDEADBEEF;
	$setvars["B"]=0x00;
	$setvars["HI"]="MOM";
	$setvars["ARRAY"]=array("Candy", foo=>"Bar");
	$setvars["cows"]="like hamburgers";
	/* We obviously don't care about previous values! */
	$APPLICATION->set($setvars);

	$getvars=array("A", "B", "HI", "ARRAY", "cows");
	$values=$APPLICATION->get($getvars);
	print "<pre>";
	print_r($values);
	print "\n\n\n";
	print "<b>All variables in this application:</b>\n";
	$allvars=$APPLICATION->getall();
	print_r($allvars);
	print "</pre>";
?>
