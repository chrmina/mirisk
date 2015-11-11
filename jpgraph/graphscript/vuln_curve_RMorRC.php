<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0);   // X axis means PGA (Measure is [g])
$ydataLR = array(0.0,0.25);
$ydataMR = array(0.0,0.30);
$ydataHR = array(0.0,0.40);  // Y axis means MDF

//create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,120,40,50);
$graph->title->Set("PGA-MDF Relation for RM or RC Structure");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot

$lineplotLR = new LinePlot($ydataLR,$xdata);
$lineplotLR->SetWeight(2);
$lineplotLR->SetColor("blue");

$lineplotMR = new LinePlot($ydataMR,$xdata);
$lineplotMR->SetWeight(2);
$lineplotMR->Setcolor("green");

$lineplotHR = new Lineplot($ydataHR,$xdata);
$lineplotHR->SetWeight(2);
$lineplotHR->Setcolor("red");

// Add plots to the graph
$graph->Add($lineplotLR);
$graph->Add($lineplotMR);
$graph->Add($lineplotHR);

// Set legend
$lineplotLR->SetLegend("Low-Rise");
$lineplotMR->SetLegend("Mid-Rise");
$lineplotHR->SetLegend("High-Rise");

$graph->legend->Pos(0.035,0.5,"right","center");

// Display the graph
$graph->Stroke();

?>