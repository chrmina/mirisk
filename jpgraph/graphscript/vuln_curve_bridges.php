<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,0.5,1.0); // X axis means PGA (Measure is [g])
$ydataConv = array(0.0,0.25,0.50);
$ydataMaj = array(0.0,0.05,0.10);   // Y axis means MDF

// create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,120,40,50);
$graph->title->Set("PGA-MDF Relation for Bridges");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot
$lineplotConv = new LinePlot($ydataConv,$xdata);
$lineplotConv->SetWeight(2);
$lineplotConv->Setcolor("red");

$lineplotMaj = new Lineplot($ydataMaj,$xdata);
$lineplotMaj->SetWeight(2);
$lineplotMaj->Setcolor("blue");

// Add plots to the graph
$graph->Add($lineplotConv);
$graph->Add($lineplotMaj);

// Set legend
$lineplotConv->SetLegend("Bridges(conventional)");
$lineplotMaj->Setlegend("Bridges(Major,Engineered,L>100m)");

$graph->legend->Pos(0.025,0.5,"right","center");

// Display the graph
$graph->Stroke();

?>