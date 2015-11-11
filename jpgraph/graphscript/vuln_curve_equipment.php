<?php

include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");

$xdata = array(0.0,1.0);   // X axis means PGA (Measure is [g])
$ydataEquipEL = array(0.0,0.30);
$ydataEquipMe = array(0.0,0.25);
$ydataEquipOther = array(0.0,0.40);  // Y axis means MDF

//create the graph

$graph = new Graph(500,300,"auto");
$graph->SetScale("linlin");
$graph->Setshadow();

$graph->img->SetMargin(60,120,40,50);
$graph->title->Set("PGA-MDF Relation for Equipment");
$graph->xaxis->title->Set("PGA [g]");
$graph->yaxis->title->Set("MDF");
$graph->yaxis->SetTitleMargin(40);
$graph->title->Setfont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->Setfont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->Setfont(FF_FONT1,FS_BOLD);

// Create the linear plot

$lineplotEquipEL = new LinePlot($ydataEquipEL,$xdata);
$lineplotEquipEL->SetWeight(2);
$lineplotEquipEL->SetColor("blue");

$lineplotEquipMe = new LinePlot($ydataEquipMe,$xdata);
$lineplotEquipMe->SetWeight(2);
$lineplotEquipMe->Setcolor("green");

$lineplotEquipOther = new Lineplot($ydataEquipOther,$xdata);
$lineplotEquipOther->SetWeight(2);
$lineplotEquipOther->Setcolor("red");

// Add plots to the graph
$graph->Add($lineplotEquipEL);
$graph->Add($lineplotEquipMe);
$graph->Add($lineplotEquipOther);

// Set legend
$lineplotEquipEL->SetLegend("Equipment(Electrical)");
$lineplotEquipMe->SetLegend("Equipment(Mechanical)");
$lineplotEquipOther->SetLegend("Equipment(Other)");

$graph->legend->Pos(0.025,0.65,"right","center");

// Display the graph
$graph->Stroke();

?>