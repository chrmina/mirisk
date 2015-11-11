<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0); // X axis means PGA (Measure is [g])
$ydataConcreteDam = array(0.0,0.10);
$ydataEarthfillDam = array(0.0,0.15);   // Y axis means MDF

// create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,120,40,50);
$graph->title->Set("PGA-MDF Relation for Dams");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot
$lineplotConcreteDam = new LinePlot($ydataConcreteDam,$xdata);
$lineplotConcreteDam->SetWeight(2);
$lineplotConcreteDam->Setcolor("blue");

$lineplotEarthfillDam = new Lineplot($ydataEarthfillDam,$xdata);
$lineplotEarthfillDam->SetWeight(2);
$lineplotEarthfillDam->Setcolor("red");

// Add plots to the graph
$graph->Add($lineplotConcreteDam);
$graph->Add($lineplotEarthfillDam);

// Set legend
$lineplotConcreteDam->SetLegend("Concrete Dams");
$lineplotEarthfillDam->Setlegend("Earthfill and Rock Fill Dams");

$graph->legend->Pos(0.025,0.7,"right","center");

// Display the graph
$graph->Stroke();

?>