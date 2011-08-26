<?php
//Include the code
include("../phplot.php");

//Define the object and get background image 0cars.jpg
//////NOTE! THIS EXAMPLE WILL ONLY WORK IF YOU HAVE 
//////JPEG SUPPORT ENABLED. (Use a different file as a background
//////if you have png or gif enabled. 
$graph = new PHPlot(500,223,"","0cars.jpg");


//Set some data
$example_data = array(
	array("55",5),
	array("60",10),
	array("65",20),
	array("70",30),
	array("75",25),
	array("80",10));
$graph->SetDataValues($example_data);

//Set up some nice formatting things
$graph->SetTitle("Speed Histogram");
$graph->SetXLabel("Miles per Hour");
$graph->SetYLabel("Percent of Cars");
$graph->SetVertTickIncrement(5);
$graph->SetPlotAreaWorld(0,0,6,35);

//Make the margins nice for the background image
$graph->SetMarginsPixels(80,35,35,70);

//Set up some color and printing options
$graph->background_done = 1;  //The image background we get from 0cars.jpg
$graph->SetDrawPlotAreaBackground(0); //Plot Area background we get from the image
$graph->SetDataColors(array("white"),array("black"));

//Set Output format
$graph->SetFileFormat("png");

//Draw it
$graph->DrawGraph();

?>
