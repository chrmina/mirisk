<?php

//get the data from posted on study data part

$the_study_oid = $_POST["selectstudy"];
$the_name = $_POST["projname"];
$the_country = $_POST["country"];
$the_eq_val = (float)$_POST["eqvalue"];
$the_wd_val = (float)$_POST["wdvalue"];
$the_fl_val = (float)$_POST["flvalue"];
$x = $_POST["xval"];
$y = $_POST["yval"];

//echo $the_study_oid;
//echo $the_name;
//echo $the_country;
//echo $the_eq_val;
//echo $the_wd_val;
//echo $the_fl_val;
//echo $x;
//echo $y;

// connect to the database
include "../processdb/connect_pg.php";

$conn = connect_pg("MIRISK");

if(!$conn){
   echo "Failed to connect to the database...";
   exit();
}

// insert data to the database(table)

/////////////////////////////////////////////////
// Add Data for project to the DB              //
/////////////////////////////////////////////////

$sql = "SELECT oid, study_id FROM studydata WHERE oid='$the_study_oid'";
$res = pg_query($conn,$sql) or die("Failed to get the data...");
$row = pg_fetch_row($res,0);
$the_study_id = $row[1];

$sql = "INSERT INTO project2 (the_geom, project_country, project_name,
        project_study_id, project_study_oid, project_eq_value,
        project_wind_value, project_flood_value)
        VALUES (GeomFromText('POINT($x $y)',4326), '$the_country', '$the_name', '$the_study_id',
        '$the_study_oid', '$the_eq_val', '$the_wd_val', '$the_fl_val');";
$res = pg_query($conn, $sql) or die("Failed to save the Project data...");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK - saveproj.php</title>\n";
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
echo "<H2><U>The following Component Data were saved successfully:</U></H2>\n";
echo "<BR><BR>\n";
echo "<U>Component Name:</U> ";
echo $the_name;
echo "<BR><BR>\n";
echo "<U>Component Country:</U> ";
echo $the_country;
echo "<BR><BR>\n";
echo "<U>Component Co-ords:</U> ";
echo "Longitute=", $x, ", Latitute=", $y;
echo "<BR><BR>\n";
echo "<U>Component Project ID:</U> ";
echo $the_study_id;
echo "<BR><BR>\n";
echo "<U>Earthquake Hazard Value (pga):</U> ";
echo $the_eq_val, " g";
echo "<BR><BR>\n";
echo "<U>Wind Hazard Value (wind speed):</U> ";
echo $the_wd_val, " km/hr";
echo "<BR><BR>\n";
//echo "<U>Flood Hazard Value (still water height):</U> ";
//echo $the_fl_val, " m";
echo "<U>Flood Hazard Level (Hotspots):</U> ";
echo $the_fl_val / 0.6;
echo "<BR><BR>\n";
echo "<center>\n";
echo "<INPUT TYPE=\"button\" value=\"Close\" onClick=\"window.close()\">\n";
echo "</center>\n";
echo "</body>\n";
echo "</html>";
?>
