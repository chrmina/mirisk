<?
/****************************************************************************
infoclick.php - takes a click point in extent coordinates ($x, $y), and spits
back the metadata of all features that intersect with a radius around the
click.
*****************************************************************************/

require_once("../includes/php/.root_config.php");
require_once("../config.php");
import_request_variables("gP", "req_");

session_set_cookie_params(7200);
session_start();

$postgis_connect_string = "host=localhost port=5432 user=pgsql password=miriskdb dbname=\"MIRISK\"";

// connect to postgis
$conn = pg_connect($postgis_connect_string);
if (!$conn) {
	die ("Connnection to database failed.");
}

echo "OK HERE";

//$GROUPVARS['DRG']['icon']="waterways.png";
//$GROUPVARS['DRG']['showlayers']=true;
//$GROUPVARS['DRG']['initialstatus']=true;
//$GROUPVARS['HSHADE']['icon']="waterways.png";
//$GROUPVARS['HSHADE']['showlayers']=true;
//$GROUPVARS['HSHADE']['initialstatus']=true;
//$GROUPVARS['GEOLOGY']['icon']="waterways.png";
//$GROUPVARS['GEOLOGY']['showlayers']=true;
//$GROUPVARS['GEOLOGY']['initialstatus']=true;

// instanciate our map object
//$map = ms_newMapObj ($ROOT."map/world.map");

// set up our request variables
//if (isSet ($_REQUEST['extent']) && 
//    isSet ($_REQUEST['image_width']) && 
//    isSet ($_REQUEST['image_height']) && 
//    isSet ($_REQUEST['coords']) && 
//    isSet ($_REQUEST['mapfile'])
//  ) {
//	$width = $_REQUEST['image_width'];
//	$height = $_REQUEST['image_height'];
//	$click = $_REQUEST['coords'];
//	$extent = $_REQUEST['extent'];
//	$mymap = $_REQUEST['mapfile'];
//} else {
//	print "<p>Invalid query string: ".$_SERVER['QUERY_STRING']."</p>";
//	exit;
//}


//$infopoint = img2map ($width, $height, $click, $extent);

//$x = $infopoint['x'];
//$y = $infopoint['y'];
//$radius = "25";

//$result = pg_list_tables ();

// go through each table in this year
//while ($data = pg_fetch_array ($result)) {
//$table = $data['name'];
//$tablename = $data['name'];
	//printData ($tablename);
//}

//print "<pre>";


// input: 
function printData ($tablename) {
	global $x, $y, $radius, $ROOT, $php_path, $req_extent;

	 list ($year, $layer) = split ("_", $tablename, 2);
	$layer = $tablename;
	 print "<p>checking layer $layer";
	 //switch ($layer) {

	 //     case "cities":
	 //$order='gid';
	 //$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
	 //$slices = pg_num_rows($result);
	 //if ($slices) {
	 //$slice = pg_fetch_array($result);
         // $glac = $slice['city_name'];

         // $temp_req = array();
         // $temp_req['city_name'] = $glac;
          //$temp_req['layer'] = $layer;
          //$temp_req['extent'] = $req_extent;
          //$_SESSION['temporal_request'] = $temp_req;
	  //print "<strong><a href='".$php_path."temporal_map.php' target='overlay_frame' onclick=\"overlay_back.style.display='block'; overlay_inner.style.display = 'block'; \">View Temporal assets</a></strong><br>";
      }	  
	        break;

		default:
			print "<p><strong>$layer</strong>: coming soon</p>\n";
		break;
	}
}



// input: 
function printData222 ($tablename) {
	global $x, $y, $radius, $ROOT, $php_path, $req_extent;

	 list ($year, $layer) = split ("_", $tablename, 2);
	$layer = $tablename;
	// print "<p>checking layer $layer";
	switch ($layer) {

	        case "cities":
	$order='gid';
	$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
      $slices = pg_num_rows($result);
      if ($slices) {
          $slice = pg_fetch_array($result);
          $glac = $slice['city_name'];

          $temp_req = array();
          $temp_req['city_name'] = $glac;
          //$temp_req['layer'] = $layer;
          //$temp_req['extent'] = $req_extent;
          //$_SESSION['temporal_request'] = $temp_req;
	  //print "<strong><a href='".$php_path."temporal_map.php' target='overlay_frame' onclick=\"overlay_back.style.display='block'; overlay_inner.style.display = 'block'; \">View Temporal assets</a></strong><br>";
      }	  
	        break;

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
	$query = "SELECT oid, city_name, cntry_name 
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
