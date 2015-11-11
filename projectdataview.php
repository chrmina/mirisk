<?php

if($_REQUEST["selectproject"]){
  $projectid = $_REQUEST["selectproject"];

  // connect to the database
  include "./processdb/connect_pg.php";

  @$conn = connect_pg("MIRISK");

  if(!$conn){
   echo "Failed to connect to the database...";
   exit();
  }

//////////////////////////////////////////
//                                      //
//  FOR FUTURE USE                      //
//                                      //
//////////////////////////////////////////

// GET the project information from MIRISK DB

// $sql = "SELECT project_study_id, project_id, project_name,
//         Astext(the_geom), project_eq_level, project_wind_level,
//         project_flood_level, project_volcano_level, asset_category,
//         category_class, project_value, project_bcr, project_eq_value,
//         k_value, project_currency, project_interest,
//         project_wind_value, project_flood_value FROM project2 WHERE project_id='$project_id'";
// @$res = pg_query($conn,$sql) or die("Failed to retrieve the project data...");
// $row = pg_fetch_row($res,0);
// $row[3] = substr($row[3],6);
// $row[3] = str_replace(")","",$row[3]);

// *** Assign data to variables

// $proj_study_id = $row[0];
// $proj_id = $row[1];
// $proj_name = $row[2];
// $coords = $row[3];
// $proj_eq_lev = $row[4];
// $proj_wind_lev = $row[5];
// $proj_flood_lev = $row[6];
// $proj_volc_lev = $row[7];
// $ass_cat = $row[8];
// $cat_class = $row[9];
// $proj_val = $row[10];
// $proj_bcr = $row[11];
// $proj_eq_val = $row[12];
// $k_val = $row[13];
// $proj_curr = $row[14];
// $proj_interest = $row[15];
// $proj_wd_val = $row[16];
// $proj_fl_val = $row[17];
// list($longit, $latit) = split(" ", $coords, 2); 
// $longit = (float)$longit;
// $latit = (float)$latit;

  $sql = "SELECT project_study_id, project_id, project_name,
          project_country, asset_category, category_class, project_value,
          project_bcr, project_eq_value, project_flood_value,
          project_wind_value, project_volcano_value, project_currency,
          project_interest, Astext(the_geom) FROM project2 WHERE project_id=$projectid";

  $res = pg_query($conn,$sql) or die("Failed to retrieve the project data");

  $fieldnum = pg_num_fields($res);

  for($j=0;$j<$fieldnum;$j++){
    $arr[$j] = pg_fetch_result($res,$j);
  }

}

 $coords= $arr[14];
 $coords = substr($coords,6);
 $coords = str_replace(")","",$coords);
  list($longit, $latit) = split(" ", $coords, 2); 
 $longit = round((float)$longit, 4);
 $latit = round((float)$latit, 4);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK</title>\n";
echo "  <meta name=\"description\" content=\"Mitigation Information and Risk Identification System\">\n";
echo "  <meta name=\"author\" content=\"MIRISK Team\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
echo "  <meta name=\"abstract\" content=\"MIRISK\">\n";
echo "  <meta name=\"description\" content=\"MIRISK\">\n";
echo "  <meta name=\"keywords\" content=\"MIRISK\">\n";
echo "  <link href=\"./dbstyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo "  <script language=\"JavaScript\" src=\"./processdb/optionsel.js\" type=\"text/javascript\"></script>\n";
echo "</head>\n";
echo "\n";
echo "<body onload=\"change_option();setoption()\">\n";
echo "\n";
//echo "<form method=\"post\" action=\"./projectdatasave.php\">\n";
echo "<form name=\"mpsel\" method =\"POST\" action=\"./processdb/projectdatasave.php\" target=\"result\" onsubmit=\"window.open('','result','width=400,height=360')\">\n";
echo "<input type=\"hidden\" name=\"projID\" value=\"", $arr[1], "\">\n";
echo "<table border=\"0\" cellpadding=\"5\">\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Component Project ID: </td>\n";
echo "    <td>", $arr[0], "</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Component ID: </td>\n";
echo "    <td>", $arr[1], "</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Component Name: </td>\n";
echo "    <td>", $arr[2], "</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Component Country: </td>\n";
echo "    <td>", $arr[3], "</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Component Co-ords: </td>\n";
echo "    <td>Longitute=", $longit, ", Latitute=", $latit, "</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Asset Category: </td>\n";
echo "    <td>\n";
echo "    <div id=\"sel1\">\n";
echo "    <select name=\"sel_1\" onchange=\"change_option()\">\n";
echo "    <option value=\"1\">---a</option>\n";
echo "    <option value=\"2\">---b</option>\n";
echo "    <option value=\"3\">---c</option>\n";
echo "    </select>\n";
echo "    </div>\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Category Class: </td>\n";
echo "    <td>\n";
echo "    <div id=\"sel2\">\n";
echo "    <select name=\"sel_2\">\n";
echo "    <option>---a</option>\n";
echo "    <option>---b</option>\n";
echo "    <option>---c</option>\n";
echo "    </select>\n";
echo "    </div>\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\">Component Value (Amount): </td>\n";
echo "    <td><INPUT TYPE=\"text\" NAME=\"value\" SIZE=\"12\" VALUE=\"", $arr[6], "\"></td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\">Currency (e.g. USD): </td>\n";
echo "    <td><INPUT TYPE=\"text\" NAME=\"currency\" SIZE=\"12\" VALUE=\"", $arr[12], "\"></td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\">Benefit to Cost Ratio (BCR, e.g. 6): </td>\n";
echo "    <td><INPUT TYPE=\"text\" NAME=\"BCrate\" SIZE=\"12\" VALUE=\"", $arr[7], "\"></td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\">Real Interest Rate (e.g. 0.03): </td>\n";
echo "    <td><INPUT TYPE=\"text\" NAME=\"interest\" SIZE=\"12\" VALUE=\"", $arr[13], "\"></td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Earthquake Hazard Value (pga): </td>\n";
echo "    <td>", $arr[8], " g</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
//echo "    <td align=\"right\" width=\"25%\">Flood Hazard Value (still water height): </td>\n";
//echo "    <td>", $arr[9], " m</td>\n";
echo "    <td align=\"right\" width=\"25%\">Flood Hazard Level (Hotspots): </td>\n";
echo "    <td>", $arr[9] / 0.6, " </td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Wind Hazard Value (wind speed): </td>\n";
echo "    <td>", $arr[10], " km/hr</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\" width=\"25%\">Volcanic Hazard Level: </td>\n";
echo "    <td>", $arr[11], "</td>\n";
echo "  </tr>\n";
//echo "  <tr>\n";
//echo "    <td colspan=\"2\" align=\"center\">\n";
//echo "        <div id=\"textinfo\">\n";
//echo "        <iframe src=\"./processdb/display_textdata.php?id=B1\" width=\"800\" height=\"500\" name=\"DispData\" frameborder=\"0\"></iframe>\n";
//echo "        </div>\n";
//echo "    </td>\n";
//echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td align=\"right\"><input type=\"submit\" name=\"save\" value=\"Save\"></td>\n";
echo "    <td><input type=\"reset\" name=\"reset\" value=\"Reset\"></td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</form>\n";  
echo "\n";
echo "</body>\n";
echo "</html>\n";
?>
