<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0);   // X axis means PGA (Measure is [g])
$ydata = array(0.0,0.20);  // Y axis means MDF

//create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");

$graph->img->SetMargin(60,50,40,50);
$graph->title->Set("PGA-MDF Relation for Canals");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot

$lineplotCanals = new LinePlot($ydata,$xdata);
$lineplotCanals->SetWeight(2);
$lineplotCanals->SetColor("blue");

// Add plots to the graph
$graph->Add($lineplotCanals);

// Display the graph
$graph->Stroke();

?>