<?
/****************************************************************************
infoclick.php - takes a click point in extent coordinates ($x, $y), and spits
back the metadata of all features that intersect with a radius around the
click.
*****************************************************************************/

//require_once("../includes/php/.root_config.php");
require_once("./config.php");
import_request_variables("gP", "req_");

session_set_cookie_params(7200);
session_start();

$postgis_connect_string = "host=localhost port=5432 user=pgsql password=miriskdb dbname=MIRISK";

// connect to postgis
$conn = pg_connect($postgis_connect_string);
if (!$conn) {
	die ("Connection to database failed.");
}

// instanciate our map object
$map = ms_newMapObj ("map/world.map");

// set up our request variables
if (isSet ($_REQUEST['extent']) && 
    isSet ($_REQUEST['image_width']) && 
    isSet ($_REQUEST['image_height']) && 
    isSet ($_REQUEST['coords']) && 
    isSet ($_REQUEST['mapfile'])
  ) {
	$width = $_REQUEST['image_width'];
	$height = $_REQUEST['image_height'];
	$click = $_REQUEST['coords'];
	$extent = $_REQUEST['extent'];
	$mymap = $_REQUEST['mapfile'];
} else {
	print "<p>Invalid query string: ".$_SERVER['QUERY_STRING']."</p>";
	exit;
}


$infopoint = img2map ($width, $height, $click, $extent);

$x = $infopoint['x'];
$y = $infopoint['y'];
$radius = "0.15";

$result = pg_list_tables ();

//echo "<U>THIS IS THE ADD PROJECT MODULE</U><BR>\n";
//echo "<P>Longitude = ", $x, "<BR>\n";
//echo "Latitute = ", $y, "</P>\n";

// go through each table in this year
while ($data = pg_fetch_array ($result)) {
$table = $data['name'];
$tablename = $data['name'];
printData ($tablename);
}

$sql = "select oid, study_name from studydata";
$res = pg_query($conn,$sql) or die("Failed to get the data...");
$rownum = pg_num_rows($res);

echo "<center>\n";
//echo "<form method=\"post\" action=\"./saveproj.php\" target=\"_blank\">\n";
echo "<form name=\"projsave\" method =\"POST\" action=\"./saveproj.php\" target=\"result\" onsubmit=\"window.open('','result','width=600,height=360')\">\n";
echo "<input type=\"hidden\" name=\"xval\" value=\"", $x, "\">\n";
echo "<input type=\"hidden\" name=\"yval\" value=\"", $y, "\">\n";
echo "<input type=\"hidden\" name=\"country\" value=\"",$the_country, "\">\n";
echo "<input type=\"hidden\" name=\"eqvalue\" value=\"",$the_eq_val, "\">\n";
echo "<input type=\"hidden\" name=\"wdvalue\" value=\"",$the_wd_val, "\">\n";
echo "<input type=\"hidden\" name=\"flvalue\" value=\"",$the_fl_val, "\">\n";
echo "<B>Input Component Name</B></BR>\n";
echo "<textarea name=\"projname\" wrap=virtual rows=1 cols=27></textarea><BR>\n";

echo "<B>Please Select a Project</B></BR>\n";
echo "<select name=\"selectstudy\">\n";
for($i=0;$i<$rownum;$i++)
{
  $arr = pg_fetch_row($res,$i);
  echo "<option value=$arr[0]>";
  echo $arr[1];
  echo "</option>\n";
}
echo "</select>\n";
echo "<BR><BR>\n";
echo "<input type=\"submit\" name=\"savedata\" value=\"Save Data\">\n";
echo "</form>\n";
echo "<BR>\n";
echo "<B><U>INSTRUCTIONS</U></B><BR>\n";
echo "</center>\n";
echo "1. Provide a name for the components in the text box.<BR>\n";
echo "2. Select the project the component belongs to from the drop down menu.<BR>\n";
echo "3. Click \"Save Data\" to store the component's data in the database.\n";

/////////////////////////////////////////////////
// Add Data for project to the DB              //
/////////////////////////////////////////////////

//$sql = "INSERT INTO project2 (the_geom, project_country, project_eq_value)
//        VALUES (GeomFromText('POINT($x $y)',4326), '$the_country', '$the_eq_val');";
//$res = pg_query($conn, $sql) or die("Failed to save the Project data...");

// input: 
function printData ($tablename)
{global $x, $y, $radius, $ROOT, $php_path, $req_extent, $the_country, $the_eq_val,
 $the_wd_val, $the_fl_val;

 $layer = $tablename;
 //print "<p>checking layer $layer";
 switch ($layer)
   {

   case "cntry02":
     $order='gid';
     $result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     $slices = pg_num_rows($result);
     if ($slices)
       {
	 $slice = pg_fetch_array($result);
	 $the_country = $slice['cntry_name'];
	 $temp_req = array();
	 $temp_req['cntry_name'] = $the_country;
	 $temp_req['layer'] = $layer;
	 $temp_req['extent'] = $req_extent;
	 $_SESSION['temporal_request'] = $temp_req;
	 //echo "Country: ",$the_country, "<BR>\n";
       }	  
     break;

   case "eq_pga":
     $order='gid';
     $result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     $slices = pg_num_rows($result);
     if ($slices)
       {
	 $slice = pg_fetch_array($result);
	 $the_eq_val = $slice['haz_val'];
	 $temp_req = array();
	 $temp_req['haz_val'] = $the_eq_val;
	 $temp_req['layer'] = $layer;
	 $temp_req['extent'] = $req_extent;
	 $_SESSION['temporal_request'] = $temp_req;
	 //echo "Eartquake Hazard (pga): ",$the_eq_val, " g<BR>\n";
       }	  
     break;

   case "cyclone":
     $order='gid';
     $result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     $slices = pg_num_rows($result);
     if ($slices)
       {
	 $slice = pg_fetch_array($result);
	 $the_wd_val = $slice['haz_val'];
	 $temp_req = array();
	 $temp_req['haz_val'] = $the_wd_val;
	 $temp_req['layer'] = $layer;
	 $temp_req['extent'] = $req_extent;
	 $_SESSION['temporal_request'] = $temp_req;
	 //echo "Tropical Cyclone Hazard (wind speed): ",$the_wd_val, " km/hr<BR>\n";
       }	  
     break;

   case "flood":
     $order='gid';
     $result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     $slices = pg_num_rows($result);
     if ($slices)
       {
	 $slice = pg_fetch_array($result);
	 $the_fl_val = $slice['haz_val'];
	 $temp_req = array();
	 $temp_req['haz_val'] = $the_fl_val;
	 $temp_req['layer'] = $layer;
	 $temp_req['extent'] = $req_extent;
	 $_SESSION['temporal_request'] = $temp_req;
	 //echo "Flood Hazard (still water height): ",$the_fl_val, " m<BR>\n";
       }	  
     break;

     //case "volcano":
     //$order='gid';
     //$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     //$slices = pg_num_rows($result);
     //if ($slices)
     //{
     // $slice = pg_fetch_array($result);
     // $glac = $slice['hazard_lv'];
     // $temp_req = array();
     // $temp_req['hazard_lv'] = $glac;
     // $temp_req['layer'] = $layer;
     // $temp_req['extent'] = $req_extent;
     // $_SESSION['temporal_request'] = $temp_req;
     // echo "Volcanic Hazard (LV): ",$glac, " <BR>\n";
     //}	  
     //break;

     //case "volcanolist":
     //$order='gid';
     //$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     //$slices = pg_num_rows($result);
     //if ($slices)
     //{
     // $slice = pg_fetch_array($result);
     // $glac = $slice['name'];
     // $temp_req = array();
     // $temp_req['name'] = $glac;
     // $temp_req['layer'] = $layer;
     // $temp_req['extent'] = $req_extent;
     // $_SESSION['temporal_request'] = $temp_req;
     // echo "Volcano Name: ",$glac, " <BR>\n";
     //}	  
     //break;

     //case "project2":
     //$order='gid';
     //$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
     //$slices = pg_num_rows($result);
     //if ($slices)
     //  {
     // $slice = pg_fetch_array($result);
     // $glac = $slice['project_name'];
     // $temp_req = array();
     // $temp_req['project_name'] = $glac;
     // $temp_req['layer'] = $layer;
     // $temp_req['extent'] = $req_extent;
     // $_SESSION['temporal_request'] = $temp_req;
     // echo "Project Name: ",$glac, " <BR>\n";
     //}	  
     //break;
     
   default:
     // print "<p><strong>$layer</strong>: coming soon</p>\n";
     break;
   }
}

function radiusQuery ($x, $y, $radius, $table) {
	global $conn;
	$query = "SELECT * 
	FROM $table
	WHERE the_geom
		&& Expand(GeomFromText('POINT($x $y)',-1), $radius)
		AND Distance(GeomFromText('POINT($x $y)',-1),the_geom) < $radius LIMIT 25;";
	// print "<pre>$query</pre>";
	return (pg_query($conn, $query));
}

function radiusQueryOrdered ($x, $y, $radius, $table, $order) {
	global $conn;
	$query = "SELECT * 
	FROM $table
	WHERE the_geom
		&& Expand(GeomFromText('POINT($x $y)',-1), $radius)
		AND Distance(GeomFromText('POINT($x $y)',-1),the_geom) < $radius ORDER BY $order;";
	// print "<pre>$query</pre>";
	return (pg_query($conn, $query));	
}


function temporalRadiusQueryOrdered ($x, $y, $radius, $table, $order) {
	global $conn;
	$query = "SELECT oid 
	FROM $table
	WHERE the_geom
		&& Expand(GeomFromText('POINT($x $y)',-1), $radius)
		AND Distance(GeomFromText('POINT($x $y)',-1),the_geom) < $radius ORDER BY $order;";
	// print "<pre>$query</pre>";
	return (pg_query($conn, $query));	
}

// utility function, lists all tables
function pg_list_tables($prefix = "") {
	global $conn;

	if ($prefix != "") {
		$prefix_and = "AND a.relname like '{$prefix}%'";
	}
	else {
		$prefix_and = "";
	}
	$sql = "SELECT a.relname AS Name
		FROM pg_class a, pg_user b
		WHERE ( relkind = 'r') and relname !~ '^pg_' AND relname !~ '^sql_'
		$prefix_and
		AND relname !~ '^xin[vx][0-9]+' AND b.usesysid = a.relowner
		AND NOT (EXISTS (SELECT viewname FROM pg_views WHERE viewname=a.relname)) ORDER BY Name DESC;";

	return(pg_query($conn, $sql));
}

/*********************************************************
  img2map:
	$width:	current image width
	$height:current image height
	$point:	click point coordinates
		array {
			0: x;
			1: y;
		}

	$ext:	current map extents
		array {
			0: minx;
			1: miny;
			2: maxx;
			3: maxy;
		}
*********************************************************/

function img2map($width, $height, $point, $ext){
	$minx = $ext['x1'];
	$miny = $ext['y1'];
	$maxx = $ext['x2'];
	$maxy = $ext['y2'];

	if($point['x1'] && $point['y1']){
		$x = $point['x1'];
		$y = $point['y1'];

		$dpp_x = ($maxx-$minx)/$width;
		$dpp_y = ($maxy-$miny)/$height;

		$x = $minx + $dpp_x*$x;
		$y = $maxy - $dpp_y*$y;
	}

	$pt['x'] = $x;
	$pt['y'] = $y;

	return $pt;
}
?>
