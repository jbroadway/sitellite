<?php
//If you have the X data out of order then you get
//weird results as shown in this example


//Include the code
include("../phplot.php");

//Define the object
$graph = new PHPlot;
$graph->SetDataType('data-data');

$graph->SetPrintImage(0);

//Set some data
$example_data = array(
	array("a",1,2),
	array("b",2,3),
	array("c",3,4),
	array("d",5,6),
	array("e",8,4),
	array("f",9,8),
	array("f",4,6)
);
$graph->SetVertTickPosition('plotleft');
$graph->SetDataValues($example_data);
$graph->SetDrawYGrid(0); 
$graph->SetPlotType("linepoints");
$graph->SetDataColors(array("orange","blue"),array("green","yellow"));
$graph->SetMarginsPixels(50,50,50,50);
$graph->SetLegend(array('Time in Flight','Time to Stop')); //Lets have a legend
//$graph->SetLegendWorld(1,8,''); //Lets have a legend position

//Draw it
$graph->DrawGraph();


$graph->PrintImage();

?>
