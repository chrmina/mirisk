<?php
// connect to the database(to get the study and project list)
//include "./connect_pg.php";
//@$conn = connect_pg("MIRISK");
//if(!$conn){
//  echo "Fail to connect to the database...";
//  exit();
//}

// connect to the MIRISK DB
// This function is for connecting to PostgreSQL DB.

 include "../jpgraph/jpgraph.php";
 include "../jpgraph/jpgraph_line.php";
 include "../jpgraph/jpgraph_scatter.php";
 include "../jpgraph/jpgraph_regstat.php";

function connect_pg($dbname)
{
  $connect_string = "host=localhost port=5432 user=pgsql password=miriskdb dbname=";
  $connect_string .= $dbname;
  return(pg_connect($connect_string));
}

 @$conn = connect_pg("MIRISK");

 if(!$conn)
{
 echo "Failed to connect to the database...";
 exit();
}

// Get the ID of saved project from user input

if(!$_POST["project"])
{
// Do nothing
  exit();
}
else
{
 $project_id = $_POST["project"];

// GET the project information from MIRISK DB
 $sql = "SELECT project_study_id, project_id, project_name,
         Astext(the_geom), project_eq_level, project_wind_level,
         project_flood_level, project_volcano_level, asset_category,
         category_class, project_value, project_bcr, project_eq_value,
         k_value, project_currency, project_interest,
         project_wind_value, project_flood_value FROM project2 WHERE project_id='$project_id'";
 @$res = pg_query($conn,$sql) or die("Failed to retrieve the project data...");
 $row = pg_fetch_row($res,0);
 $row[3] = substr($row[3],6);
 $row[3] = str_replace(")","",$row[3]);
// Assign data to variables
 $proj_study_id = $row[0];
 $proj_id = $row[1];
 $proj_name = $row[2];
 $coords = $row[3];
 $proj_eq_lev = $row[4];
 $proj_wind_lev = $row[5];
 $proj_flood_lev = $row[6];
 $proj_volc_lev = $row[7];
 $ass_cat = $row[8];
 $cat_class = $row[9];
 $proj_val = $row[10];
 $proj_bcr = $row[11];
 $proj_eq_val = $row[12];
 $k_val = $row[13];
 $proj_curr = $row[14];
 $proj_interest = $row[15];
 $proj_wd_val = $row[16];
 $proj_fl_val = $row[17];
 list($longit, $latit) = split(" ", $coords, 2); 
 $longit = (float)$longit;
 $latit = (float)$latit;

// GET the study information from MIRISK DB
 $sql2 = "SELECT study_id, study_name, study_country, study_location, study_components,
          study_start, study_end, team_leader, team_members,
          other_notes FROM studydata WHERE study_id='$proj_study_id'";
 @$res2 = pg_query($conn,$sql2) or die("Failed to retrieve the study data...");
 $row2 = pg_fetch_row($res2,0);
// Assign data to variables
 $stud_name = $row2[1];
 $stud_country = $row2[2];
 $stud_loc = $row2[3];
 $stud_comp = $row2[4];
 $stud_start = $row2[5];
 $stud_end = $row2[6];
 $stud_lead = $row2[7];
 $stud_memb = $row2[8];
 $stud_notes = $row2[9];

// GET the asset information from MIRISK DB
 $sql3 = "SELECT id, \"Asset_Class\",
          \"EQ_Description\", \"EQ_Damage\", \"EQ_Design\",
          \"Wind_Description\", \"Wind_Damage\", \"Wind_Design\",
          \"Flood_Description\", \"Flood_Damage\", \"Flood_Description\",
          \"Photo Description Path\", \"Photo Description Caption\", \"Photo Description Credit\",
          \"Photo WD Path\", \"Photo WD Caption\", \"Photo WD Credit\",
          \"Photo WM Path\", \"Photo WM Caption\", \"Photo WM Credit\",
          \"Photo ED Path\", \"Photo ED Caption\", \"Photo ED Credit\",
          \"Photo EM Path\", \"Photo EM Caption\", \"Photo EM Credit\",
          \"Photo FD Path\", \"Photo FD Caption\", \"Photo FD Credit\",
          \"Photo FM Path\", \"Photo FM Caption\", \"Photo FM Credit\"
          FROM assets2 WHERE id='$cat_class'";
 @$res3 = pg_query($conn,$sql3) or die("Failed to retrieve the asset data...");
 $row3 = pg_fetch_row($res3,0);
// Assign data to variables
 $ass_class_desc = $row3[1];
 $ass_eq_desc = $row3[2];
 $ass_eq_damage = $row3[3];
 $ass_eq_design = $row3[4];
 $ass_wind_desc = $row3[5];
 $ass_wind_damage = $row3[6];
 $ass_wind_design = $row3[7];
 $ass_flood_desc = $row3[8];
 $ass_flood_damage = $row3[9];
 $ass_flood_design = $row3[10];
 $ass_ph_desc_path = $row3[11];
 $ass_ph_desc_capt = $row3[12];
 $ass_ph_desc_cred = $row3[13];
 $ass_ph_wd_path = $row3[14];
 $ass_ph_wd_capt = $row3[15];
 $ass_ph_wd_cred = $row3[16];
 $ass_ph_wm_path = $row3[17];
 $ass_ph_wm_capt = $row3[18];
 $ass_ph_wm_cred = $row3[19];
 $ass_ph_ed_path = $row3[20];
 $ass_ph_ed_capt = $row3[21];
 $ass_ph_ed_cred = $row3[22];
 $ass_ph_em_path = $row3[23];
 $ass_ph_em_capt = $row3[24];
 $ass_ph_em_cred = $row3[25];
 $ass_ph_fd_path = $row3[26];
 $ass_ph_fd_capt = $row3[27];
 $ass_ph_fd_cred = $row3[28];
 $ass_ph_fm_path = $row3[29];
 $ass_ph_fm_capt = $row3[30];
 $ass_ph_fm_cred = $row3[31];

// GET the Vulnerability Function information for EQ
 $sql4 = "SELECT \"CatClass\", \"pga0.00\",
          \"pga0.05\", \"pga0.10\", \"pga0.22\",
          \"pga0.47\", \"pga1.02\", \"kec\"
          FROM \"VulnFunctEQ\" WHERE \"CatClass\"='$cat_class'";
 @$res4 = pg_query($conn,$sql4) or die("Failed to retrieve the EQ Vulneraibiliy data...");
 $row4 = pg_fetch_row($res4,0);
// Assign data to variables
 $vuln_pga000 = $row4[1];
 $vuln_pga005 = $row4[2];
 $vuln_pga010 = $row4[3];
 $vuln_pga022 = $row4[4];
 $vuln_pga047 = $row4[5];
 $vuln_pga102 = $row4[6];
 $vuln_kec = $row4[7];

// GET the Vulnerability Function information for Wind
 $sql5 = "SELECT \"CatClass\", \"Speed0\",
          \"Speed100\", \"Speed150\", \"Speed200\",
          \"Speed250\", \"Speed300\", \"kwc\"
          FROM \"VulnFunctWind\" WHERE \"CatClass\"='$cat_class'";
 @$res5 = pg_query($conn,$sql5) or die("Failed to retrieve the EQ Vulneraibiliy data...");
 $row5 = pg_fetch_row($res5,0);
// Assign data to variables
 $vuln_speed0 = $row5[1];
 $vuln_speed100 = $row5[2];
 $vuln_speed150 = $row5[3];
 $vuln_speed200 = $row5[4];
 $vuln_speed250 = $row5[5];
 $vuln_speed300 = $row5[6];
 $vuln_kwc = $row5[7];

// GET the Vulnerability Function information for Flood
 $sql6 = "SELECT \"CatClass\", \"Height0.0\",
          \"Height0.5\", \"Height1.0\", \"Height2.0\",
          \"Height4.0\", \"Height6.0\", \"kfc\"
          FROM \"VulnFunctFlood\" WHERE \"CatClass\"='$cat_class'";
 @$res6 = pg_query($conn,$sql6) or die("Failed to retrieve the EQ Vulneraibiliy data...");
 $row6 = pg_fetch_row($res6,0);
// Assign data to variables
 $vuln_height00 = $row6[1];
 $vuln_height05 = $row6[2];
 $vuln_height10 = $row6[3];
 $vuln_height20 = $row6[4];
 $vuln_height40 = $row6[5];
 $vuln_height60 = $row6[6];
 $vuln_kfc = $row6[7];

///////////////////////////////////////////
//                                       //
//   CALCULATIONS                        //
//                                       //
///////////////////////////////////////////

 function lininterp($x, $x1, $y1, $x2, $y2)
 {
  $y = ($x-$x1)*($y1-$y2)/($x1-$x2) + $y1;
  return($y);
 }

 //$b = 1.0;
 $realinter = $proj_interest;

///////////////////////////////////////////
//   EARTHQUAKE                          //
///////////////////////////////////////////

 $fixedpr = 1/475;
 if ($proj_eq_val > 0)
{
// *** AEL Calculations
 $EQ_AEL = 0.0;
 $proj_eq_maxval = 1.4 * $proj_eq_val;
 for ($haz = 0.001; $haz <= 1.000; $haz += 0.001)
 {
   //$probexc = ($proj_eq_val ^ $b) * $fixedpr / ($haz ^ $b);
   if ($haz == 0.001)
     {
       $probexc0 = 1.0;
       $probexc1 = $proj_eq_val * $fixedpr / ($haz + 0.001);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   if ($haz > 0.001)
     {
       $probexc0 = $proj_eq_val * $fixedpr / ($haz - 0.001);
       //$probexc0 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz - 0.001), $b));
       $probexc1 = $proj_eq_val * $fixedpr / ($haz + 0.001);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   $probexc =  ($probexc0 - $probexc1) / 2;
   if ($haz > 0.0 and $haz <= 0.05)
     {
       $dmg = lininterp($haz, 0.00, $vuln_pga000, 0.05, $vuln_pga005);
     }
   if ($haz > 0.05 and $haz <= 0.10)
     {
       $dmg = lininterp($haz, 0.05, $vuln_pga005, 0.10, $vuln_pga010);
     }
   if ($haz > 0.10 and $haz <= 0.22)
     {
       $dmg = lininterp($haz, 0.10, $vuln_pga010, 0.22, $vuln_pga022);
     }
   if ($haz > 0.22 and $haz <= 0.47)
     {
       $dmg = lininterp($haz, 0.22, $vuln_pga022, 0.47, $vuln_pga047);
     }
   if ($haz > 0.47 and $haz <= 1.02)
     {
       $dmg = lininterp($haz, 0.47, $vuln_pga047, 1.02, $vuln_pga102);
     }
   //$file = "./graphs/newfile.txt";   
   //if (!$file_handle = fopen($file,"a")) { echo "Cannot open file"; }  
   //if (!fwrite($file_handle, "$haz, $probexc0, $probexc1, $probexc, $dmg\n")) { echo "Cannot write to file"; }  
   $EQ_AEL += $probexc * $dmg;
 }
 //echo "You have successfully written data to $file";
 //fclose($file_handle);

 // *** Loss Calculations
 $EQ_dirloss = $EQ_AEL / $realinter;
 $EQ_indirloss = $EQ_dirloss * $proj_bcr;
 $EQ_totloss = $EQ_dirloss + $EQ_indirloss;

 // *** Graph calculations

 // data points
 $EQ_opt_totcost=(double)1E+30;
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_eq_val * $desfact;
     $constcost = 1 + $vuln_kec * pow(($hazval - $proj_eq_val) / ($proj_eq_maxval - $proj_eq_val),2);
     $damage = $EQ_totloss * pow(((1.4 - $desfact) / (1.4 - 1.0)),2);
     $totcost = $constcost + $damage;
     if ($desfact == 1)
       {
	 $EQ_base_totcost = $totcost;
       }
     if ($totcost < $EQ_opt_totcost)
       {
	 $EQ_opt_totcost = $totcost;
	 $EQ_opt_constcost= $constcost;
	 $EQ_opt_desfact = $desfact;
       }
     $xdata[]= $desfact;
     $ydelconstr[] = round(($proj_val * ($constcost -1)), 2);
     $ytotalloss[] = round(($proj_val * $damage), 2);
     $ytotal[] = round((($proj_val * ($constcost -1)) + ($proj_val * $damage)), 2);
   } 

 // Splines

 $spline1 = new Spline($xdata, $ydelconstr);
 $spline2 = new Spline($xdata, $ytotalloss);
 $spline3 = new Spline($xdata, $ytotal);

 // Create the graph
 $graph = new Graph(810, 600, "auto");
 $graph->SetMargin(100, 120, 50, 90);
 $graph->title->Set("Cost Benefit Analysis (EQ)");
 $graph->title->SetFont(FF_FONT1,FS_NORMAL,12);
 $graph->SetMarginColor('lightblue');

 // Set the scale 
 $graph->Setscale('linlin', 0, 0, 1.0, 1.4);

 // Set the title
 $graph->xaxis->title->Set("Design Level Factor");
 $graph->yaxis->title->Set("Cost [$proj_curr]");
 $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->yaxis->SetTitleMargin(60);
 $graph->xaxis->SetTitleMargin(20);
 
 $graph->xaxis->SetPos(1);
 $graph->xaxis->SetLabelFormat('%1.2f');

 $slplot1 = new Lineplot($ydelconstr, $xdata);
 $slplot2 = new Lineplot($ytotalloss, $xdata);
 $slplot3 = new Lineplot($ytotal, $xdata);

 $graph->Add($slplot1);
 $graph->Add($slplot2);
 $graph->Add($slplot3);

 $slplot1->Setcolor("blue");
 $slplot2->Setcolor("green");
 $slplot3->Setcolor("red");

 $slplot1->SetLegend("Additional Construction Cost");
 $slplot2->SetLegend("Expected Damage from Earthquake");
 $slplot3->SetLegend("Total Cost");

 $graph->legend->Pos(0.05, 0.1, "right", "center");

 $myfile= "./graphs/EQgraph.png";
 $graph->Stroke($myfile);
}
///////////////////////////////////////////
//   WIND                                //
///////////////////////////////////////////

 $fixedpr = 1/100;
 if ($proj_wd_val > 0)
{
// *** AEL Calculations
 $WD_AEL = 0.0;
 $proj_wd_maxval = 1.4 * $proj_wd_val;
 for ($haz = 0.2; $haz <= 300; $haz += 0.2)
 {
   //$probexc = ($proj_eq_val ^ $b) * $fixedpr / ($haz ^ $b);
   if ($haz == 0.2)
     {
       $probexc0 = 1.0;
       $probexc1 = $proj_wd_val * $fixedpr / ($haz + 0.2);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   if ($haz > 0.2)
     {
       $probexc0 = $proj_wd_val * $fixedpr / ($haz - 0.2);
       //$probexc0 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz - 0.001), $b));
       $probexc1 = $proj_wd_val * $fixedpr / ($haz + 0.2);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   $probexc =  ($probexc0 - $probexc1) / 2;
   if ($haz > 0.0 and $haz <= 100.0)
     {
       $dmg = lininterp($haz, 0.00, $vuln_speed0, 100.0, $vuln_speed100);
     }
   if ($haz > 100.0 and $haz <= 150.0)
     {
       $dmg = lininterp($haz, 100.0, $vuln_speed100, 150.0, $vuln_speed150);
     }
   if ($haz > 150.0 and $haz <= 200.0)
     {
       $dmg = lininterp($haz, 150.0, $vuln_speed150, 200.0, $vuln_speed200);
     }
   if ($haz > 200.0 and $haz <= 250.0)
     {
       $dmg = lininterp($haz, 200.0, $vuln_speed200, 250.0, $vuln_speed250);
     }
   if ($haz > 250.0 and $haz <= 300.0)
     {
       $dmg = lininterp($haz, 250.0, $vuln_speed250, 300.0, $vuln_speed300);
     }
   //$file = "./graphs/newfile.txt";   
   //if (!$file_handle = fopen($file,"a")) { echo "Cannot open file"; }  
   //if (!fwrite($file_handle, "$haz, $probexc0, $probexc1, $probexc, $dmg\n")) { echo "Cannot write to file"; }  
   $WD_AEL += $probexc * $dmg;
 }
 //echo "You have successfully written data to $file";
 //fclose($file_handle);

 // *** Loss Calculations
 $WD_dirloss = $WD_AEL / $realinter;
 $WD_indirloss = $WD_dirloss * $proj_bcr;
 $WD_totloss = $WD_dirloss + $WD_indirloss;

 // *** Graph calculations

 // data points
 unset($xdata);
 unset($ydelconstr);
 unset($ytotalloss);
 unset($ytotal);  

 $WD_opt_totcost=(double)1E+30;
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_wd_val * $desfact;
     $constcost = 1 + $vuln_kwc * pow(($hazval - $proj_wd_val) / ($proj_wd_maxval - $proj_wd_val),2);
     $damage = $WD_totloss * pow(((1.4 - $desfact) / (1.4 - 1.0)),2);
     $totcost = $constcost + $damage;
     if ($desfact == 1)
       {
	 $WD_base_totcost = $totcost;
       }
     if ($totcost < $WD_opt_totcost)
       {
	 $WD_opt_totcost = $totcost;
	 $WD_opt_constcost= $constcost;
	 $WD_opt_desfact = $desfact;
       }
     $xdata[]= $desfact;
     $ydelconstr[] = round(($proj_val * ($constcost -1)), 2);
     $ytotalloss[] = round(($proj_val * $damage), 2);
     $ytotal[] = round((($proj_val * ($constcost -1)) + ($proj_val * $damage)), 2);
   } 

 // Splines

 $spline1 = new Spline($xdata, $ydelconstr);
 $spline2 = new Spline($xdata, $ytotalloss);
 $spline3 = new Spline($xdata, $ytotal);

 // Create the graph
 $graph = new Graph(810, 600, "auto");
 $graph->SetMargin(100, 120, 50, 90);
 $graph->title->Set("Cost Benefit Analysis (Wind)");
 $graph->title->SetFont(FF_FONT1,FS_NORMAL,12);
 $graph->SetMarginColor('lightblue');

 // Set the scale 
 $graph->Setscale('linlin', 0, 0, 1.0, 1.4);

 // Set the title
 $graph->xaxis->title->Set("Design Level Factor");
 $graph->yaxis->title->Set("Cost [$proj_curr]");
 $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->yaxis->SetTitleMargin(60);
 $graph->xaxis->SetTitleMargin(20);
 
 $graph->xaxis->SetPos(1);
 $graph->xaxis->SetLabelFormat('%1.2f');

 $slplot1 = new Lineplot($ydelconstr, $xdata);
 $slplot2 = new Lineplot($ytotalloss, $xdata);
 $slplot3 = new Lineplot($ytotal, $xdata);

 $graph->Add($slplot1);
 $graph->Add($slplot2);
 $graph->Add($slplot3);

 $slplot1->Setcolor("blue");
 $slplot2->Setcolor("green");
 $slplot3->Setcolor("red");

 $slplot1->SetLegend("Additional Construction Cost");
 $slplot2->SetLegend("Expected Damage from Wind");
 $slplot3->SetLegend("Total Cost");

 $graph->legend->Pos(0.05, 0.1, "right", "center");

 $myfile= "./graphs/WDgraph.png";
 $graph->Stroke($myfile);
}

///////////////////////////////////////////
//   FLOOD                               //
///////////////////////////////////////////

 $fixedpr = 1/475;
 if ($proj_fl_val > 0)
{
// *** AEL Calculations
 $FL_AEL = 0.0;
 $proj_fl_maxval = 1.4 * $proj_fl_val;

 for ($haz = 0.005; $haz <= 6; $haz += 0.005)
 {
   //$probexc = ($proj_eq_val ^ $b) * $fixedpr / ($haz ^ $b);
   if ($haz == 0.005)
     {
       $probexc0 = 1.0;
       $probexc1 = $proj_fl_val * $fixedpr2 / ($haz + 0.005);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   if ($haz > 0.005)
     {
       $probexc0 = $proj_fl_val * $fixedpr2 / ($haz - 0.005);
       //$probexc0 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz - 0.001), $b));
       $probexc1 = $proj_fl_val * $fixedpr2 / ($haz + 0.005);
       //$probexc1 = (double) (pow($proj_eq_val, $b) * $fixedpr / (pow(($haz + 0.001), $b));
     }
   $probexc =  ($probexc0 - $probexc1) / 2;
   if ($haz > 0.0 and $haz <= 0.5)
     {
       $dmg = lininterp($haz, 0.00, $vuln_height00, 0.5, $vuln_height05);
     }
   if ($haz > 0.5 and $haz <= 1.0)
     {
       $dmg = lininterp($haz, 0.5, $vuln_height05, 1.0, $vuln_height10);
     }
   if ($haz > 1.0 and $haz <= 2.0)
     {
       $dmg = lininterp($haz, 1.0, $vuln_height10, 2.0, $vuln_height20);
     }
   if ($haz > 2.0 and $haz <= 4.0)
     {
       $dmg = lininterp($haz, 2.0, $vuln_height20, 4.0, $vuln_height40);
     }
   if ($haz > 4.0 and $haz <= 6.0)
     {
       $dmg = lininterp($haz, 4.0, $vuln_height40, 6.0, $vuln_height60);
     }
   //$file = "./graphs/newfile.txt";   
   //if (!$file_handle = fopen($file,"a")) { echo "Cannot open file"; }  
   //if (!fwrite($file_handle, "$haz, $probexc0, $probexc1, $probexc, $dmg\n")) { echo "Cannot write to file"; }  
   $FL_AEL += $probexc * $dmg;
 }
 //echo "You have successfully written data to $file";
 //fclose($file_handle);

 // *** Loss Calculations
 $FL_dirloss = $FL_AEL / $realinter;
 $FL_indirloss = $FL_dirloss * $proj_bcr;
 $FL_totloss = $FL_dirloss + $FL_indirloss;

 // *** Graph calculations

 // data points
 unset($xdata);
 unset($ydelconstr);
 unset($ytotalloss);
 unset($ytotal);  

 $FL_opt_totcost=(double)1E+30;
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_fl_val * $desfact;
     $constcost = 1 + $vuln_kfc * pow(($hazval - $proj_fl_val) / ($proj_fl_maxval - $proj_fl_val),2);
     $damage = $FL_totloss * pow(((1.4 - $desfact) / (1.4 - 1.0)),2);
     $totcost = $constcost + $damage;
     if ($desfact == 1)
       {
	 $FL_base_totcost = $totcost;
       }
     if ($totcost < $FL_opt_totcost)
       {
	 $FL_opt_totcost = $totcost;
	 $FL_opt_constcost= $constcost;
	 $FL_opt_desfact = $desfact;
       }
     $xdata[]= $desfact;
     $ydelconstr[] = round(($proj_val * ($constcost -1)), 2);
     $ytotalloss[] = round(($proj_val * $damage), 2);
     $ytotal[] = round((($proj_val * ($constcost -1)) + ($proj_val * $damage)), 2);
   } 

 // Splines

 $spline1 = new Spline($xdata, $ydelconstr);
 $spline2 = new Spline($xdata, $ytotalloss);
 $spline3 = new Spline($xdata, $ytotal);

 // Create the graph
 $graph = new Graph(810, 600, "auto");
 $graph->SetMargin(100, 120, 50, 90);
 $graph->title->Set("Cost Benefit Analysis (Flood)");
 $graph->title->SetFont(FF_FONT1,FS_NORMAL,12);
 $graph->SetMarginColor('lightblue');

 // Set the scale 
 $graph->Setscale('linlin', 0, 0, 1.0, 1.4);

 // Set the title
 $graph->xaxis->title->Set("Design Level Factor");
 $graph->yaxis->title->Set("Cost [$proj_curr]");
 $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
 $graph->yaxis->SetTitleMargin(60);
 $graph->xaxis->SetTitleMargin(20);
 
 $graph->xaxis->SetPos(1);
 $graph->xaxis->SetLabelFormat('%1.2f');

 $slplot1 = new Lineplot($ydelconstr, $xdata);
 $slplot2 = new Lineplot($ytotalloss, $xdata);
 $slplot3 = new Lineplot($ytotal, $xdata);

 $graph->Add($slplot1);
 $graph->Add($slplot2);
 $graph->Add($slplot3);

 $slplot1->Setcolor("blue");
 $slplot2->Setcolor("green");
 $slplot3->Setcolor("red");

 $slplot1->SetLegend("Additional Construction Cost");
 $slplot2->SetLegend("Expected Damage from Flood");
 $slplot3->SetLegend("Total Cost");

 $graph->legend->Pos(0.05, 0.1, "right", "center");

 $myfile= "./graphs/FLgraph.png";
 $graph->Stroke($myfile);
}

// *** Output & Report

 echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
 echo "<html>\n";
 echo "<head>\n";
 echo "  <title>MIRISK - Analysis and Report</title>\n";
 echo "  <meta name=\"description\" content=\"Mitigation Information and Risk Identification System\">\n";
 echo "  <meta name=\"author\" content=\"MIRISK Team\">\n";
 echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
 echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
 echo "  <meta name=\"abstract\" content=\"MIRISK\">\n";
 echo "  <meta name=\"description\" content=\"MIRISK\">\n";
 echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
 echo "  <link href=\"../dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
 echo "</head>\n";
 echo "<body>\n";

 // Report Heading
 echo "<TABLE WIDTH=\"800\" BORDER=\"0\">\n";
 echo "<TR><TD ALIGN=\"right\">\n";
 //echo "<form>\n";
 echo "<img src=\"../images/printicon.gif\" onClick=\"window.print()\">";
 echo "<img src=\"../images/pdficon.gif\">\n";
 // echo "<input type=\"button\" value=\"Print This Page\" onClick=\"window.print()\">\n";
 //echo "</form>\n";
 echo "</TD>\n";
 echo "</TR>\n";
 echo "</TABLE>\n";
 echo "<CENTER>\n";
 echo "<H1><B>MIRISK ANALYSIS AND REPORT</B></H1><BR>\n";
 echo "</CENTER>\n";
 echo "<div id=\"divider\"></div>\n"; 

 // Study Data
 echo "<H1><U>Project Information</U></H1><BR>\n";
 echo "<B><U>Project Name:</U></B> ", $stud_name, "<BR><BR>\n";
 echo "<B><U>Project ID:</U></B> ", $proj_study_id, "<BR><BR>\n";
 echo "<B><U>Project Location:</U></B> ", $stud_country, ", ", $stud_loc, "<BR><BR>\n";
 echo "<B><U>Project Components:</U></B> ", $stud_comp, "<BR><BR>\n";
 echo "<B><U>Project Start Date:</U></B> ", $stud_start, "<BR><BR>\n";
 echo "<B><U>Project Completion Date:</U></B> ", $stud_end, "<BR><BR>\n";
 echo "<B><U>Project Team Members:</U></B> ", $stud_memb, "<BR><BR>\n";
 echo "<B><U>Project Team Leader:</U></B> ", $stud_lead, "<BR><BR>\n";
 echo "<B><U>Other Notes:</U></B> ", $stud_notes, "<BR><BR>\n";
 // Project Data
 echo "<H1><U>Component Information</U></H1><BR>\n";
 echo "<B><U>Component Name:</U></B> ", $proj_name, "<BR><BR>\n";
 echo "<B><U>Component ID:</U></B> ", $proj_id, "<BR><BR>\n";
 echo "<B><U>Component Location:</U></B> ", $stud_country, "<BR><BR>\n";
 echo "<B><U>Component Co-ordinates:</U></B> Longitute=", round($longit, 6), ", Latitute=", round($latit, 6), "<BR><BR>\n";
 echo "<B><U>Component Site Earthquake Hazard (pga):</U></B> ", round($proj_eq_val,3), " g<BR><BR>\n";
 echo "<B><U>Component Site Wind Hazard (wind speed):</U></B> ", round($proj_wd_val,0), " km/hr<BR><BR>\n";
 // echo "<B><U>Project Site Flood Hazard Level (still water height):</U></B> ", round($proj_fl_val,2), " m<BR><BR>\n";
 echo "<B><U>Component Site Flood Hazard Level (Hotspots):</U></B> ", round($proj_fl_val / 0.6, 2), " <BR><BR>\n";
 // Asset Related Info
 echo "<H2><U><A NAME=\"infotop\">Asset Related Information</A></U></H2><BR>\n";
 echo "<B><U>Asset Value:</U></B> ", $proj_curr, " ",  $proj_val, "<BR><BR>\n";
 //echo "<B><U>Currency:</U></B> ", $proj_curr, "<BR><BR>\n";
 echo "<B><U>Asset Benefit to Cost Ratio:</U></B> ", $proj_bcr, "<BR><BR>\n";
 echo "<B><U>Real Interest Rate:</U></B> ", $proj_interest, "<BR><BR>\n";
 echo "<B><U>Asset Category:</U></B> ", $ass_cat, "<BR><BR>\n";
 echo "<B><U>Category Class:</U></B> ", $ass_class_desc, "<BR><BR>\n";
 //echo "<img src=\"",$ass_ph_desc_path,"\"><BR><BR>\n";
 //echo $ass_ph_desc_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_desc_cred ,"\n";
 //
 //echo "<H2><U>Table of Contents</U></H2>\n";
 //echo "<UL>\n";
 //echo "<LI><A HREF=\"#EQ-Description\">Earthquake Description</A><BR>\n";
 //echo "<LI><A HREF=\"#EQ-Performance\">Earthquake Performance</A><BR>\n";
 //echo "<LI><A HREF=\"#EQ-Design\">Earthquake Design</A><BR>\n";
 //echo "</UL>\n";
 //
 //echo "<UL>\n";
 //echo "<LI><A HREF=\"#Wind-Description\">Wind Description</A><BR>\n";
 //echo "<LI><A HREF=\"#Wind-Performance\">Wind Performance</A><BR>\n";
 //echo "<LI><A HREF=\"#Wind-Design\">Wind Design</A><BR>\n";
 //echo "</UL>\n";
 //
 //echo "<UL>\n";
 //echo "<LI><A HREF=\"#Flood-Description\">Flood Description</A><BR>\n";
 //echo "<LI><A HREF=\"#Flood-Performance\">Flood Performance</A><BR>\n";
 //echo "<LI><A HREF=\"#Flood-Design\">Flood Design</A><BR>\n";
 //echo "</UL>\n";
 //
 //echo "<H2>Earthquake</H2>\n";
 //echo "<H3><A NAME=\"EQ-Description\">Earthquake Description</A></H3>\n";
 //echo $ass_eq_desc;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"EQ-Performance\">Typical Seismic Damage and Performance</A></H3>\n";
 //echo "<img src=\"",$ass_ph_ed_path,"\"><BR><BR>\n";
 //echo $ass_ph_ed_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_ed_cred,"<BR><BR>\n";
 //echo $ass_eq_damage;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"EQ-Design\">Seismic Resistant Design</A></H3>\n";
 //echo "<img src=\"",$ass_ph_em_path,"\"><BR><BR>\n";
 //echo $ass_ph_em_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_em_cred,"<BR><BR>\n";
 //echo $ass_eq_design;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //
 //echo "<H2>Wind</H2>\n";
 //echo "<H3><A NAME=\"Wind-Description\">Wind Description</A></H3>\n";
 //echo $ass_wind_desc;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"Wind-Performance\">Typical Wind Damage and Performance</A></H3>\n";
 //echo "<img src=\"",$ass_ph_wd_path,"\"><BR><BR>\n";
 //echo $ass_ph_wd_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_wd_cred,"<BR><BR>\n";
 //echo $ass_wind_damage;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"Wind-Design\">Wind Resistant Design</A></H3>";
 //echo "<img src=\"",$ass_ph_wm_path,"\"><BR><BR>\n";
 //echo $ass_ph_wm_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_wm_cred,"<BR><BR>\n";
 //echo $ass_wind_design;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //
 //echo "<H2>Flood</H2>\n";
 //echo "<H3><A NAME=\"Flood-Description\">Flood Description</A></H3>\n";
 //echo $ass_flood_desc;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"Flood-Performance\">Typical Flood Damage and Performance</A></H3>\n";
 //echo "<img src=\"",$ass_ph_fd_path,"\"><BR><BR>\n";
 //echo $ass_ph_fd_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_fd_cred,"<BR><BR>\n";
 //echo $ass_flood_damage;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";
 //echo "<H3><A NAME=\"Flood-Design\">Flood Resistant Design</A></H3>";
 //echo "<img src=\"",$ass_ph_fm_path,"\"><BR><BR>\n";
 //echo $ass_ph_fm_capt,"<BR>\n";
 //echo "<U>Photo Source:</U> ",$ass_ph_fm_cred,"<BR><BR>\n";
 //echo $ass_flood_design;
 //echo "<H6>[<A HREF=\"#infotop\">Back to Top</A>]</H6>\n";

 // Cost Benefits Analysis Results
 echo "<H1><U>Cost Benefits Analysis</U></H1><BR>\n";
 // Earthquake Analysis
 echo "<H2><U><A NAME=\"EQ-Analysis\">Earthquake Analysis</A></U></H2><BR>\n";
 if ($proj_eq_val > 0)
{
 echo "<B><U>Component Site Earthquake Hazard Value (pga):</U></B> ", $proj_eq_val, " g<BR><BR>\n";
 echo "<B><U>Results</U></B><BR>\n";
 echo "<P>\n";
 echo "(1) EAL = Expected Annual Loss = ", round($EQ_AEL, 4), " (i.e., ", round($EQ_AEL*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(2) PV(Direct Losses) = (1) / (Real Interest Rate) = ", round($EQ_dirloss, 4), " (i.e., ", round($EQ_dirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(3) PV(Indirect Losses) = (2) x BCR = ", round($EQ_indirloss, 4), " (i.e., ", round($EQ_indirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(4) PV(Total Losses) = (2) + (3) = ", round($EQ_totloss, 4), " (i.e., ", round($EQ_totloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(5) Optimum Design Level = ", $EQ_opt_desfact, " (i.e., ", round(($EQ_opt_desfact-1)*100,0), "% above minimum code design)<BR>\n";
 echo "(6) Additional Constr. Cost at Optimum = (", $proj_curr,") ", round($EQ_opt_constcost*$proj_val - $proj_val, 0), " (i.e. ", round((($EQ_opt_constcost*$proj_val - $proj_val)/$proj_val)*100, 0), "%)<BR>\n";
 echo "(7) Total Savings = TC(1.0) - TC(opt) = (", $proj_curr,") ", round(($EQ_base_totcost - $EQ_opt_totcost) * $proj_val, 0), " (i.e. ", round((($EQ_base_totcost - $EQ_opt_totcost)/$EQ_base_totcost)*100, 0), "%)<BR><BR>\n";
 echo "</P>\n";
 echo "<CENTER>\n";
 echo "    <TABLE WIDTH=800 border=1>\n";
 echo "    <TR>\n";
 echo "      <TH WIDTH=9%>Design Level Multiplier</TH>\n";
 echo "      <TH WIDTH=13%>Construction Cost<BR>(", $proj_curr, ")</TH> \n";
 echo "      <TH WIDTH=13%>Increase in Cost<BR>(From the base design cost)<BR>(", $proj_curr, ")</TH> \n";
 //echo "      <TH WIDTH=13%>Benefit<BR>(", $proj_curr, ")</TH>\n";
 //echo "      <TH WIDTH=13%>Direct Loss</TH>\n";
 //echo "      <TH WIDTH=13%>Indirect Loss</TH>\n";
 echo "      <TH WIDTH=13%>Total Loss<BR>(", $proj_curr, ")</TH>\n";
 echo "      <TH WIDTH=13%>Total Cost<BR>(", $proj_curr, ")</TH>\n";
 echo "    </TR>\n";
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_eq_val * $desfact;
     $constcost = 1 + $vuln_kec * pow(($hazval - $proj_eq_val) / ($proj_eq_maxval - $proj_eq_val),2);
     $damage = $EQ_totloss * pow((1.4 - $desfact) / (1.4 - 1.0),2);
     $totcost = $constcost + $damage;
     if ($desfact == $EQ_opt_desfact)
       {
	 echo "    <TR BGCOLOR=\"yellow\">\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 //echo "      <TD align=center>", number_format(round(($proj_val * $proj_bcr), 2)), "</TD>\n";
	 //echo "      <TD align=right>", round($dir_loss, 2), "</TD>\n";
	 //echo "      <TD align=right>", round($indir_loss, 2), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
     else
       {
	 echo "    <TR>\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 //echo "      <TD align=center>", number_format(round(($proj_val * $proj_bcr), 2)), "</TD>\n";
	 //echo "      <TD align=right>", round($dir_loss, 2), "</TD>\n";
	 //echo "      <TD align=right>", round($indir_loss, 2), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
   } 
 echo "    </TABLE>\n";
 echo "</CENTER>\n";
 echo "<BR><BR>\n";
 // Start Graph
 echo "<CENTER>\n";
 echo "<img src=\"./graphs/EQgraph.png\">\n";
 echo "</CENTER>\n"; 
 // End Graph
}
else
{
  echo "<H3>Earthquake Analysis Not Applicable.</H3>\n";
}
 
// Wind Analysis
 echo "<H2><U><A NAME=\"WD-Analysis\">Wind Analysis</A></U></H2><BR>\n";
 if ($proj_wd_val > 0)
{
 echo "<B><U>Component Site Wind Hazard Value:</U></B> ", round($proj_wd_val, 1), " km/hr<BR><BR>\n";
 echo "<B><U>Results</U></B><BR>\n";
 echo "<P>\n";
 echo "(1) EAL = Expected Annual Loss = ", round($WD_AEL, 4), " (i.e., ", round($WD_AEL*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(2) PV(Direct Losses) = (1) / (Real Interest Rate) = ", round($WD_dirloss, 4), " (i.e., ", round($WD_dirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(3) PV(Indirect Losses) = (2) x BCR = ", round($WD_indirloss, 4), " (i.e., ", round($WD_indirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(4) PV(Total Losses) = (2) + (3) = ", round($WD_totloss, 4), " (i.e., ", round($WD_totloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(5) Optimum Design Level = ", $WD_opt_desfact, " (i.e., ", round(($WD_opt_desfact-1)*100,0), "% above minimum code design)<BR>\n";
 echo "(6) Additional Constr. Cost at Optimum = (", $proj_curr,") ", round($WD_opt_constcost*$proj_val - $proj_val, 0), " (i.e. ", round((($WD_opt_constcost*$proj_val - $proj_val)/$proj_val)*100, 0), "%)<BR>\n";
 echo "(7) Total Savings = TC(1.0) - TC(opt) = (", $proj_curr,") ", round(($WD_base_totcost - $WD_opt_totcost) * $proj_val, 0), " (i.e. ", round((($WD_base_totcost - $WD_opt_totcost)/$WD_base_totcost)*100, 0), "%)<BR><BR>\n";
 echo "</P>\n";
 echo "<CENTER>\n";
 echo "    <TABLE WIDTH=800 border=1>\n";
 echo "    <TR>\n";
 echo "      <TH WIDTH=9%>Design Level Multiplier</TH>\n";
 echo "      <TH WIDTH=13%>Construction Cost<BR>(", $proj_curr, ")</TH> \n";
 echo "      <TH WIDTH=13%>Increase in Cost<BR>(From the base design cost)<BR>(", $proj_curr, ")</TH> \n";
 echo "      <TH WIDTH=13%>Total Loss<BR>(", $proj_curr, ")</TH>\n";
 echo "      <TH WIDTH=13%>Total Cost<BR>(", $proj_curr, ")</TH>\n";
 echo "    </TR>\n";
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_wd_val * $desfact;
     $constcost = 1 + $vuln_kwc * pow(($hazval - $proj_wd_val) / ($proj_wd_maxval - $proj_wd_val),2);
     $damage = $WD_totloss * pow((1.4 - $desfact) / (1.4 - 1.0),2);
     $totcost = $constcost + $damage;
     if ($desfact == $WD_opt_desfact)
       {
	 echo "    <TR BGCOLOR=\"yellow\">\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
     else
       {
	 echo "    <TR>\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
   } 
 echo "    </TABLE>\n";
 echo "</CENTER>\n";
 echo "<BR><BR>\n";
 // Start Graph
 echo "<CENTER>\n";
 echo "<img src=\"./graphs/WDgraph.png\">\n";
 echo "</CENTER>\n"; 
 // End Graph
}
else
{
  echo "<H3>Wind Analysis Not Applicable.</H3>\n";
}

 // Flood Analysis
 echo "<H2><U><A NAME=\"FL-Analysis\">Flood Analysis</A></U></H2><BR>\n";
 if ($proj_fl_val > 0)
{
  // echo "<B><U>Project Site Flood Hazard Value (wind speed):</U></B> ", round($proj_fl_val, 1), " m<BR><BR>\n";
 echo "<B><U>Component Site Flood Hazard Value (Hotspots):</U></B> ", round($proj_fl_val / 0.6, 1), " <BR><BR>\n";
 echo "<B><U>Results</U></B><BR>\n";
 echo "<P>\n";
 echo "(1) EAL = Expected Annual Loss = ", round($FL_AEL, 4), " (i.e., ", round($FL_AEL*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(2) PV(Direct Losses) = (1) / (Real Interest Rate) = ", round($FL_dirloss, 4), " (i.e., ", round($FL_dirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(3) PV(Indirect Losses) = (2) x BCR = ", round($FL_indirloss, 4), " (i.e., ", round($FL_indirloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(4) PV(Total Losses) = (2) + (3) = ", round($FL_totloss, 4), " (i.e., ", round($FL_totloss*100,3), "%) @ design level factor=1.0<BR>\n";
 echo "(5) Optimum Design Level = ", $FL_opt_desfact, " (i.e., ", round(($FL_opt_desfact-1)*100,0), "% above minimum code design)<BR>\n";
 echo "(6) Additional Constr. Cost at Optimum = (", $proj_curr,") ", round($FL_opt_constcost*$proj_val - $proj_val, 0), " (i.e. ", round((($FL_opt_constcost*$proj_val - $proj_val)/$proj_val)*100, 0), "%)<BR>\n";
 echo "(7) Total Savings = TC(1.0) - TC(opt) = (", $proj_curr,") ", round(($FL_base_totcost - $FL_opt_totcost) * $proj_val, 0), " (i.e. ", round((($FL_base_totcost - $FL_opt_totcost)/$FL_base_totcost)*100, 0), "%)<BR><BR>\n";
 echo "</P>\n";
 echo "<CENTER>\n";
 echo "    <TABLE WIDTH=800 border=1>\n";
 echo "    <TR>\n";
 echo "      <TH WIDTH=9%>Design Level Multiplier</TH>\n";
 echo "      <TH WIDTH=13%>Construction Cost<BR>(", $proj_curr, ")</TH> \n";
 echo "      <TH WIDTH=13%>Increase in Cost<BR>(From the base design cost)<BR>(", $proj_curr, ")</TH> \n";
 echo "      <TH WIDTH=13%>Total Loss<BR>(", $proj_curr, ")</TH>\n";
 echo "      <TH WIDTH=13%>Total Cost<BR>(", $proj_curr, ")</TH>\n";
 echo "    </TR>\n";
 for ( $desfact = 1; $desfact <= 1.41; $desfact += 0.01)
   {
     $hazval = $proj_fl_val * $desfact;
     $constcost = 1 + $vuln_kfc * pow(($hazval - $proj_fl_val) / ($proj_fl_maxval - $proj_fl_val),2);
     $damage = $FL_totloss * pow((1.4 - $desfact) / (1.4 - 1.0),2);
     $totcost = $constcost + $damage;
     if ($desfact == $FL_opt_desfact)
       {
	 echo "    <TR BGCOLOR=\"yellow\">\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
     else
       {
	 echo "    <TR>\n";
	 echo "      <TD align=center>", $desfact, "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $constcost), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * ($constcost - 1)), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $damage), 0)), "</TD>\n";
	 echo "      <TD align=center>", number_format(round(($proj_val * $totcost), 0)), "</TD>\n";
	 echo "    </TR>\n";
       }
   } 
 echo "    </TABLE>\n";
 echo "</CENTER>\n";
 echo "<BR><BR>\n";
 // Start Graph
 echo "<CENTER>\n";
 echo "<img src=\"./graphs/FLgraph.png\">\n";
 echo "</CENTER>\n"; 
 // End Graph
}
else
{
 echo "<H3>Flood Analysis Not Applicable.</H3>\n";
}

 // Volcanic Analysis
 echo "<H2><U><A NAME=\"VL-Analysis\">Volcanic Hazard Analysis</A></U></H2><BR>\n";
 if ($proj_vl_val > 0)
{
  // Do Some Analysis HERE
}
else
{
 echo "<H3>Volcanic Hazard Analysis Not Applicable.</H3>\n";
}
 echo "<H5><I>END OF REPORT</I></H5>\n";
 echo "</body>\n";
 echo "</html>\n";
}
?>
