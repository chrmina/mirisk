<?php
// load the module
include("../../jpgraph/jpgraph.php");
include("../../jpgraph/jpgraph_line.php");
include("../../jpgraph/jpgraph_utils.inc");



// get the parameter from form

$a = $_POST["quartic_4th"];
$b = $_POST["quartic_3rd"];
$c = $_POST["quartic_2nd"];
$d = $_POST["quartic_1st"];
$e = $_POST["quartic_0th"];

// get the point list

for($i=0;$i<51;$i++){
  $xdata[$i] = 0.02*$i;
  $ydata[$i] = $a*pow($xdata[$i],4) + $b*pow($xdata[$i],3) + $c*pow($xdata[$i],2) + $d*$xdata[$i] + $e;
}

$lplot = new LinePlot($ydata,$xdata);
$lplot->SetColor("red");

$graph = new Graph(600,400);
$graph->Setscale("linlin");

$graph->Add($lplot);
$graph->Stroke();

?>