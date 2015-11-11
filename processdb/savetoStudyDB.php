<?php

//get the data from posted on study data part

$ID = $_POST["ID"];
$name = $_POST["name"];
$begindate = $_POST["begin"];
$deadline = $_POST["deadline"];
$country = $_POST["cboCountry"];
$district = $_POST["district"];
$components = $_POST["components"];
$members = $_POST["members"];
$leader = $_POST["leader"];
$notes = $_POST["notes"];

// connect to the database
include "./connect_pg.php";

$conn = connect_pg("MIRISK");

if(!$conn){
   echo "Failed to connect to the database...";
   exit();
}

// insert data to the database(table)
$sql = "INSERT INTO studydata(study_id, study_name, study_start, study_end, study_country, study_location, study_components,team_members,team_leader, other_notes) values('$ID', '$name', '$begindate', '$deadline', '$country', '$district', '$components','$members', '$leader', '$notes')";

$res = pg_query($conn,$sql) or die("Failed to save the data to the database...");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK - savetoStudyDB.php</title>\n";
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
echo "<H2>The following Project Data were saved successfully:</H2>\n";
echo "<BR><BR>\n";
echo "<U>Project ID:</U> ";
echo $ID;
echo "<BR><BR>\n";
echo "<U>Project Name:</U> ";
echo $name;
echo "<BR><BR>\n";
echo "<U>Start Date:</U> ";
echo $begindate;
echo "<BR><BR>\n";
echo "<U>Completion Date:</U> ";
echo $deadline;
echo "<BR><BR>\n";
echo "<U>Country:</U> ";
echo $country;
echo "<BR><BR>\n";
echo "<U>Location:</U> ";
echo $district;
echo "<BR><BR>\n";
echo "<U>Project Components (i.e. Assets):</U> ";
echo $components;
echo "<BR><BR>\n";
echo "<U>Project Team Members:</U> ";
echo $members;
echo "<BR><BR>\n";
echo "<U>Project Team Leader:</U> ";
echo $leader;
echo "<BR><BR>\n";
echo "<U>Other notes:</U> ";
echo $notes;
echo "<BR><BR>\n";
echo "<A HREF=\"javascript:javascript:history.go(-1)\">Back to the previous page.</A>\n";
?>
