<?php
//Include the code
include("../phplot.php");

//Define the object
$graph = new PHPlot;

//Set some data
$example_data = array(
	array('',3,4,5),
	array('',3,4,5),
	array('',3,4,5),
	array('',4,5,7)
);
$graph->SetDataValues($example_data);
//Error_Reporting(0);
$graph->SetPlotType('pie');
$graph->SetLabelScalePosition(1.3);
$graph->SetLegend(array('Blue Data','Green Data','Yellow Data'));


//Draw it
$graph->DrawGraph();

?>
