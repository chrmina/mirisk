<?php

 if($_REQUEST["selectstudydel"])
{
  $OIDvalue = $_REQUEST["selectstudydel"];

// connect to the database
 include "./connect_pg.php";

 @$conn = connect_pg("MIRISK");

 if(!$conn)
   {
     echo "Fail to connect to the database";
     exit();
   }

 $sql1 = "DELETE from project2 where project_study_oid='$OIDvalue'";
 $sql2 = "DELETE from studydata where oid='$OIDvalue'";

 $res1 = pg_query($conn,$sql1) or die("Failed to delete the projects associated with the study...");
 $res2 = pg_query($conn,$sql2) or die("Failed to delete the study from the database...");

 echo "<html>\n";
 echo "<head>\n";
 echo "  <title>MIRISK - deletestudy.php</title>\n";
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
 echo "<B>The Project and its components were DELETED Successfully!</B><BR><BR>\n";
 echo "<A HREF=\"javascript:javascript:history.go(-1)\">Back to previous page.</A>\n";
 echo "</center>\n";
 echo "</body>\n";
}

?>
