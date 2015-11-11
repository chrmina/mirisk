<?php

 $projectid = $_REQUEST["projID"];

// connect to the database
 include "./connect_pg.php";
 $conn = connect_pg("MIRISK");
 if(!$conn)
 {
  echo "Fail to connect to the database";
  exit();
 }

// GET the project information from MIRISK DB

 $sql = "SELECT project_study_id, project_id, project_name,
         Astext(the_geom), project_eq_value, project_wind_value,
         project_flood_value, project_volcano_value, asset_category,
         category_class, project_value, project_bcr, project_currency, project_interest,
         project_country FROM project2 WHERE project_id='$projectid'";
 $res = pg_query($conn,$sql) or die("Failed to retrieve the project data...");
 $row = pg_fetch_row($res,0);
 $row[3] = substr($row[3],6);
 $row[3] = str_replace(")","",$row[3]);

// *** Assign data to variables
 $proj_study_id = $row[0];
 $proj_id = $row[1];
 $proj_name = $row[2];
 $coords = $row[3];
 $proj_eq_val = $row[4];
 $proj_wind_val = $row[5];
 $proj_flood_val = $row[6];
 $proj_volc_val = $row[7];
 $ass_cat = $row[8];
 $cat_class = $row[9];
 $proj_val = $row[10];
 $proj_bcr = $row[11];
 $proj_curr = $row[12];
 $proj_interest = $row[13];
 $proj_country = $row[14];
 list($longit, $latit) = split(" ", $coords, 2); 
 $longit = (float)$longit;
 $latit = (float)$latit;

// *** Get Asset Class Description
 $sql = "SELECT \"Asset_Class\" FROM assets2 WHERE id='$cat_class'";
 $res = pg_query($conn,$sql) or die("Failed to get the asset class data...");
 $cat_class_desc = pg_fetch_result($res,0);

 echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
 echo "<html>\n";
 echo "<head>\n";
 echo "  <title>MIRISK - requestprojinfo.php</title>\n";
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
 echo "\n";
 echo "<CENTER>\n";
 echo "<H2><U>COMPONENT INFORMATION</U></H2><BR>\n";
 echo "<table border=\"0\" cellpadding=\"5\">\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Component Project ID: </td>\n";
 echo "    <td>", $proj_study_id, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Component ID: </td>\n";
 echo "    <td>", $proj_id, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Component Name: </td>\n";
 echo "    <td>", $proj_name, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Component Country: </td>\n";
 echo "    <td>", $proj_country, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Component Co-ords: </td>\n";
 echo "    <td>Longitute=", $longit, ", Latitute=", $latit, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Asset Category: </td>\n";
 echo "    <td>", $ass_cat, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Category Class: </td>\n";
 echo "    <td>", $cat_class, ", ", $cat_class_desc, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\">Component Value (Amount): </td>\n";
 echo "    <td>(", $proj_curr, ") ", $proj_val, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\">Benefit to Cost Ratio (BCR): </td>\n";
 echo "    <td>", $proj_bcr, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\">Real Interest Rate: </td>\n";
 echo "    <td>", $proj_interest, "</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Earthquake Hazard Value (pga): </td>\n";
 echo "    <td>", $proj_eq_val, " g</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
//echo "    <td align=\"right\" width=\"25%\">Flood Hazard Value (still water height): </td>\n";
//echo "    <td>", $proj_flood_val, " m</td>\n";
 echo "    <td align=\"right\" width=\"25%\">Flood Hazard Level (Hotspots): </td>\n";
 echo "    <td>", $proj_flood_val / 0.6, " </td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Wind Hazard Value (wind speed): </td>\n";
 echo "    <td>", $proj_wind_val, " km/hr</td>\n";
 echo "  </tr>\n";
 echo "  <tr>\n";
 echo "    <td align=\"right\" width=\"25%\">Volcanic Hazard Level: </td>\n";
 echo "    <td>", $proj_volc_val, "</td>\n";
 echo "  </tr>\n";
 echo "</table>\n";
 echo "<CENTER>\n";
 echo "<INPUT TYPE=\"button\" value=\"Close\" onClick=\"window.close()\">\n";
 echo "</CENTER>\n";
 echo "</body>\n";
 echo "</html>\n";
?>
