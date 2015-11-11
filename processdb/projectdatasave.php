<?php

// Collect the data from the form

 $projID = $_POST["projID"];
 $assetcat = $_POST["sel_1"];
 $catclass = $_POST["sel_2"];
 $projvalue = $_POST["value"];
 $projbcr = $_POST["BCrate"];
 $projcurr = $_POST["currency"];
 $projinterest = $_POST["interest"];
//echo $projID, "\n";
//echo $assetcat, "\n";
//echo $catclass, "\n";
//echo $projvalue, "\n";
//echo $projbcr, "\n";

// connect to the database
 include "./connect_pg.php";

 @$conn = connect_pg("MIRISK");

 if(!$conn)
{
  echo "Failed to connect to the database...";
  exit();
}

 $sql1 = "SELECT \"k_Factor\" FROM assets2 WHERE id='$catclass'";

 $res = pg_query($conn,$sql1) or die("Failed to retrieve the data...");
 $kfact = pg_fetch_result($res,0);

// insert data to the database(table)
 $sql2 = "UPDATE project2 SET asset_category='$assetcat', category_class='$catclass',
          project_value=$projvalue, project_bcr=$projbcr, k_value=$kfact,
          project_currency='$projcurr', project_interest=$projinterest WHERE project_id=$projID";

 $res = pg_query($conn,$sql2) or die("Failed to save the data to the database...");

 echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
 echo "<html>\n";
 echo "<head>\n";
 echo "  <title>MIRISK - projectdatasave.php</title>\n";
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
 echo "<H2><U>Component Data Saved Successfully:</U></H2>\n";
 echo "<BR><BR>\n";
 echo "<U><B>Component ID:</B></U> ";
 echo $projID;
 echo "<BR><BR>\n";
 echo "<U><B>Asset Category:</B></U> ";
 echo $assetcat;
 echo "<BR><BR>\n";
 echo "<U><B>Category Class:</B></U> ";
 echo $catclass;
 echo "<BR><BR>\n";
 echo "<U><B>Component Value:</B></U> ";
 echo $projvalue;
 echo "<BR><BR>\n";
 echo "<U><B>Currency:</B></U> ";
 echo $projcurr;
 echo "<BR><BR>\n";
 echo "<U><B>Component Benefit to Cost Ratio:</B></U> ";
 echo $projbcr;
 echo "<BR><BR>\n";
 echo "<U><B>Real Interest Rate:</B></U> ";
 echo $projinterest;
 echo "<BR><BR>\n";
 echo "<CENTER>\n";
 echo "<INPUT TYPE=\"button\" value=\"Close\" onClick=\"window.close()\">\n";
 echo "</CENTER>\n";
 //echo "<A HREF=\"javascript:javascript:history.go(-1)\">Back to the previous page.</A>\n";
 echo "</BODY>\n";
 echo "</HTML>\n";
?>
