<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0);   // X axis means PGA (Measure is [g])
$ydataUnder = array(0.0,0.10);
$ydataGround = array(0.0,0.20);
$ydataElevated = array(0.0,0.40);  // Y axis means MDF

//create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,140,40,50);
$graph->title->Set("PGA-MDF Relation for Tanks");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot

$lineplotUnder = new LinePlot($ydataUnder,$xdata);
$lineplotUnder->SetWeight(2);
$lineplotUnder->SetColor("blue");

$lineplotGround = new LinePlot($ydataGround,$xdata);
$lineplotGround->SetWeight(2);
$lineplotGround->Setcolor("green");

$lineplotElevated = new Lineplot($ydataElevated,$xdata);
$lineplotElevated->SetWeight(2);
$lineplotElevated->Setcolor("red");

// Add plots to the graph
$graph->Add($lineplotUnder);
$graph->Add($lineplotGround);
$graph->Add($lineplotElevated);

// Set legend
$lineplotUnder->SetLegend("Tanks Underground");
$lineplotGround->SetLegend("Tanks and Basins on Ground");
$lineplotElevated->SetLegend("Tanks Elevated");

$graph->legend->Pos(0.025,0.375,"right","center");

// Display the graph
$graph->Stroke();

?>