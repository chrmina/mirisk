<?php 

// check the posted value
$value = $_POST["value"];
$BCrate = $_POST["BCrate"];
$Assetcat = $_POST["Assetcat"];
$catclass = $_POST["catclass"];
$projname = $_POST["project_name"];
$study_id = $_POST["study_id"];
$project_id = $_POST["project_id"];
$PGA = $_POST["PGA"];
$Kvalue = $_POST["Kvalue"];

// connect to the MIRISK database
include "./connect_pg.php";

@$conn = connect_pg("MIRISK");

if(!$conn){
  echo "Fail to connect to the database";
  exit();
}

//insert the value into database(project table)
@$sql = "UPDATE project SET asset_category = '$Assetcat', category_class = '$catclass',
                 value = $value, bc_rate = $BCrate, study_id = $study_id, 
                 name = '$projname', pga = $PGA, Kvalue=$Kvalue
                 where project_id = $project_id";

$res = pg_query($conn,$sql);

if(!$res){
  echo "Fail to save the data.";
}else{
  echo "saved in database correctly.";
}

?>

<html>
<head><title></title></head>
<body>
<center>
  <INPUT TYPE="button" value="close" onClick="window.close()">
</center>
</body>
</html>