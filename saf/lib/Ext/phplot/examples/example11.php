<?php
//Include the code
include("../phplot.php");

//Define the object
$graph = new PHPlot;

$graph->SetPrintImage(0);

//Set some data
$example_data = array(
	array("a",1,2),
	array("b",2,2),
	array("c",3,2),
	array("d",5,2),
	array("e",8,2),
	array("f",9,2),
	array("f",4,2)
);
$graph->SetPlotAreaWorld(0,0,7.5,10);
$graph->SetVertTickPosition('plotleft');
$graph->SetSkipBottomTick(1);
$graph->SetDataValues($example_data);
$graph->SetDrawYGrid(0); 
$graph->SetPlotType('bars');
$graph->SetDrawDataLabels('1');
$graph->SetLabelScalePosition('1');
$graph->SetDataColors(array("orange","blue"),array("green","yellow"));
$graph->SetMarginsPixels(50,50,50,50);
$graph->SetLegend(array('Time in Flight','Time to Stop')); //Lets have a legend
$graph->SetLegendWorld(1,8); //Lets have a legend position

//Draw it
$graph->DrawGraph();

//////////////////////NEXT SETTINGS

$example_data = array(
	array("a",60),
	array("b",40),
	array("c",50),
	array("d",50),
	array("e",80),
	array("f",90),
	array("f",40)
);
$graph->SetDataValues($example_data);
$graph->SetDataColors(array("red"),array("green"));

$graph->SetDrawXDataLabels(0); //We already got them in the first graph
$graph->SetPlotAreaWorld(0,0,7.5,100); //New Plot Area
$graph->SetLegend(array('Size of Dog')); //Lets add a second legend
$graph->DrawLegend(55,55,'');

//Set Params of another Y Axis
$graph->SetVertTickPosition('yaxis');
$graph->SetYGridLabelType('right');
$graph->SetYAxisPosition(7.5);
$graph->SetPrecisionY(0);
$graph->SetTickColor('red');
$graph->SetTextColor('red');
$graph->SetGridColor('red');
$graph->DrawYAxis();

//Draw the New data over the first graph
$graph->DrawLines();
$graph->DrawDots();



$graph->PrintImage();

?>
