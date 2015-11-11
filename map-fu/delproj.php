<?php

//get the data from posted on study data part

$projid = $_POST["projectid"];

// connect to the database
include "../processdb/connect_pg.php";

$conn = connect_pg("MIRISK");

if(!$conn){
   echo "Failed to connect to the database...";
   exit();
}

/////////////////////////////////////////////////
// Delete the project from the DB              //
/////////////////////////////////////////////////

$sql1 = "SELECT * FROM project2 WHERE project_id='$projid'";
$res1 = pg_query($conn,$sql1) or die("Failed to get the project data from the database...");
$row = pg_fetch_row($res1,0);
$test = $row[0];

if(!$test){
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  echo "<html>\n";
  echo "<head>\n";
  echo "  <title>MIRISK - ERROR!</title>\n";
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
  echo "<center>\n";
  echo "<H2><U>ERROR!</U></H2><BR>\n";
  echo "<H4>A Component with ID = ", $projid, "<BR>\n";
  echo "could not be found in the database.</H4>\n";
  echo "<BR>\n";
  echo "<INPUT TYPE=\"button\" value=\"Close\" onClick=\"window.close()\">\n";
  echo "</center>\n";
  echo "</body>\n";
  echo "</html>";
  exit();
}

$sql = "DELETE from project2 where project_id='$projid'";
$res = pg_query($conn,$sql) or die("Failed to delete the project from the database...");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "  <title>MIRISK - delproj.php</title>\n";
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
echo "<center>\n";
echo "<H2><U>SUCCESS!</U></H2><BR>\n";
echo "<H4>The Component with ID = ", $projid, "<BR>\n";
echo " was DELETED successfully!</H4>\n";
echo "<BR><BR>\n";
echo "<INPUT TYPE=\"button\" value=\"Close\" onClick=\"window.close()\">\n";
echo "</center>\n";
echo "</body>\n";
echo "</html>";
?>
