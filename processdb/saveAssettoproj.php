<?php
$value = $_POST["value"];
$BCrate = $_POST["BCrate"];

$cat = $_POST[sel_1];
$classID = $_POST[sel_2];

// connect to the database to get the K-value
// from asset table

include "./connect_pg.php";

@$conn = connect_pg("MIRISK");

if(!$conn){
   echo "Fail to connect to the database";
   exit();
}

$sql = "select structure_type_or_component ,K_factor from assets where ID = '$classID'";

$res = pg_query($conn,$sql) or die("fail to load the data");

$arr = pg_fetch_array($res,0);

$class = $arr[0];
$Kfactor = $arr[1];

echo "Asset Category: ";
echo $cat;
echo "<BR>";
echo "Category Class: ";
echo $class;
echo "<BR>";
echo "Input value: ";
echo $value;
echo "<BR>";
echo "Input BCrate: ";
echo $BCrate;
?>

<form method="POST" action="saveAssettoproj2.php">
  <input type="hidden" name="value" value=<?php echo $value; ?>>
  <input type="hidden" name="BCrate" value=<?php echo $BCrate; ?>>
  <input type="hidden" name="Assetcat" value=<?php echo $cat; ?>>
  <input type="hidden" name="catclass" value=<?php echo $class; ?>>
  input the id to connect the data<BR>
  project_name:<input type=text name="project_name"><BR>
  study_id:<input type=text name="study_id"><BR>
  project_id(from the label of project point on the map):<input type=text name="project_id"><BR>
  PGA(eventually given from PGA database or dbf.file):<input type=text name="PGA"><BR>
  K-value(eventually from Asset database):<input type=text name="Kvalue" value=<?php echo $Kfactor; ?>><BR>
  <input type="submit" value="save to the database">
</form>