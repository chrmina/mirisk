<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0); // X axis means PGA (Measure is [g])
$ydataPipeOrdinary = array(0.0,0.05);
$ydataPipeLiquefiable = array(0.0,0.20);   // Y axis means MDF

// create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,120,40,50);
$graph->title->Set("PGA-MDF Relation for Pipelines");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot
$lineplotPipeOrdinary = new LinePlot($ydataPipeOrdinary,$xdata);
$lineplotPipeOrdinary->SetWeight(2);
$lineplotPipeOrdinary->Setcolor("blue");

$lineplotPipeLiquefiable = new Lineplot($ydataPipeLiquefiable,$xdata);
$lineplotPipeLiquefiable->SetWeight(2);
$lineplotPipeLiquefiable->Setcolor("red");

// Add plots to the graph
$graph->Add($lineplotPipeOrdinary);
$graph->Add($lineplotPipeLiquefiable);

// Set legend
$lineplotPipeOrdinary->SetLegend("Pipelines(ordinary soil)");
$lineplotPipeLiquefiable->Setlegend("Pipelines(liquefiable soil)");

$graph->legend->Pos(0.025,0.5,"right","center");

// Display the graph
$graph->Stroke();

?>