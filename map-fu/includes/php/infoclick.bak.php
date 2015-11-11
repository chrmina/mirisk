<?
/****************************************************************************
infoclick.php - takes a click point in extent coordinates ($x, $y), and spits
back the metadata of all features that intersect with a radius around the
click.
*****************************************************************************/

require_once(".root_config.php");
require_once($ROOT."config.php");

// connect to postgis
$conn = pg_connect($postgis_connect_string);
if (!$conn) {
	die ("Connnection to database failed.");
}

$VARS['mapfile'] = $ROOT."map/world.map";

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
$map = ms_newMapObj ($VARS['mapfile']);

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
$radius = "25";

$result = pg_list_tables ();

// go through each table in this year
while ($data = pg_fetch_array ($result)) {
	$table = $data['name'];
	$tablename = $data['name'];
	printData ($tablename);
}

print "<pre>";

// input: 
function printData ($tablename) {
	global $x, $y, $radius;

	// list ($year, $layer) = split ("_", $tablename, 2);
	$layer = $tablename;
	// print "<p>checking layer $layer";
	switch ($layer) {



//		case "huc_5_pnw":
//			$result = radiusQuery ($x, $y, $radius, $tablename);
//			while ($data = pg_fetch_array ($result)) {
//				print "<strong>watershed:</strong> ".$data['watershed']."<br>";
//				print "<strong>watershed_:</strong> ".$data['watershed_']."<br>";
//				print "<strong>ds_huc5:</strong> ".$data['ds_huc5']."<br>";
//				print "<strong><a href='http://geospatial.research.pdx.edu/glaciers/assets/index.php?huc_id=".$data['watershed']."' target='_blank'>assets</a></strong><br>";
//			}
//		break;

		case "assreg":
			$order = "reg_level";
//			print $order;
			$result = radiusQueryOrdered ($x, $y, $radius, $tablename, $order);
			while ($data = pg_fetch_array ($result)) {
			  switch ($data['reg_level']){
			   case 1:
  				print "<strong>State:</strong> ".$data['reg_name']."<br>";
  				print "<strong><a href='http://geospatial.research.pdx.edu/glaciers/assets/index.php?region_id=".$data['id']. "&region_level=" . $data['reg_level'] . "' target='_blank'>Show assets</a></strong><br>";
          break;
         case 2:
  				print "<strong>Region:</strong> ".$data['reg_name']."<br>";
  				print "<strong><a href='http://geospatial.research.pdx.edu/glaciers/assets/index.php?region_id=" . $data['id']. "&region_level=" . $data['reg_level'] . "' target='_blank'>Show assets</a></strong><br>";
          break;
         case 3:
  				print "<strong>Glacier:</strong> ".$data['reg_name']."<br>";
  				print "<strong><a href='http://geospatial.research.pdx.edu/glaciers/assets/index.php?region_id=" . $data['id']. "&region_level=" . $data['reg_level'] . "' target='_blank'>Show assets</a></strong><br>";
//  				print "<strong><a href='http://geospatial.research.pdx.edu/glaciers/assets/index.php?region_id=" . $data['id']. "&region_level=" . $data['reg_level'] . ">Show assets</a></strong><br>";
          break;
        }
			}
		break;


		case "glacs24kb":
			$result = radiusQuery ($x, $y, $radius, $tablename);
			while ($data = pg_fetch_array ($result)) {
				print "<p><strong>24K Glaciers</strong>:</p>\n";
				print "<font size=-1>";
				print "<strong>Name:</strong> ".$data['glacname']."<br>";
				// print "<strong>recno:</strong> ".$data['recno']."<br>";
				print "<strong>Longitude:</strong> ".$data['x_coord']."<br>";
				print "<strong>Latitude:</strong> ".$data['y_coord']."<br>";
				// print "<strong>classifica:</strong> ".$data['classifica']."<br>";
				//print "<strong>year:</strong> ".$data['year']."<br>";
				//print "<strong>source_sca:</strong> ".$data['source_sca']."<br>";
				print "<strong>Source:</strong> ".$data['source']."<br>";
				//print "<strong>comment:</strong> ".$data['comment']."<br>";
				//print "<strong>usgs_qd_id:</strong> ".$data['usgs_qd_id']."<br>";
				//print "<strong>region:</strong> ".$data['region']."<br>";
				//print "<strong>state:</strong> ".$data['state']."<br>";
				print "<strong>State:</strong> ".$data['statename']."<br>";
				print "<strong>Map Quad:</strong> ".$data['quadname']."<br>";
				//print "<strong>filename:</strong> ".$data['filename']."<br>";
				//print "<strong>publicatio:</strong> ".$data['publicatio']."<br>";
				print "<strong>Photodate:</strong> ".$data['photodate1']."<br>";
				//print "<strong>photomin:</strong> ".$data['photomin']."<br>";
				//print "<strong>photomax:</strong> ".$data['photomax']."<br>";
				//print "<strong>source_1:</strong> ".$data['source_1']."<br>";
				//print "<strong>comments:</strong> ".$data['comments']."<br>";
				print "<strong>Area (m<SUP>2</SUP>):</strong> ".round($data['area'])."<br>";
				print "<strong>Min Elevation (m):</strong> ".round($data['elev_min'])."<br>";
				print "<strong>Max Elevation (m):</strong> ".round($data['elev_max'])."<br>";
				//print "<strong>Elevation Range(m):</strong> ".round($data['el_rang'])."<br>";
				print "<strong>Mean Elevation (m):</strong> ".round($data['elev_mean'])."<br>";
				//print "<strong>el_std:</strong> ".round($data['el_std'])."<br>";
				//print "<strong>el_sum:</strong> ".round($data['el_sum'])."<br>";
				//print "<strong>sl_min:</strong> ".round($data['sl_min'])."<br>";
				//print "<strong>sl_max:</strong> ".round($data['sl_max'])."<br>";
				//print "<strong>sl_rang:</strong> ".round($data['sl_rang'])."<br>";
				print "<strong>Mean Slope (deg):</strong> ".round($data['slp_d_mean'])."<br>";
				//print "<strong>sl_std:</strong> ".round($data['sl_std'])."<br>";
				print "<strong>Mean Aspect (deg):</strong> ".round($data['asp_mean'])."<br>";
				print "</font>";
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
