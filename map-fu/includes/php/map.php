<?php
 /***********************************************************************
 *
 * @file          map.php
 *
 * $Id: map.php 110 2007-02-25 02:42:36Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/map.php $
 *
 * @project       Map-Fu
 *
 * This project was developed as part of the Oregon Sustainable
 * Community Digital Library (OSCDL) by Academic & Research Computing
 * at Portland State University with support by Oregon State
 * Library grants 245020, 245021.  Special thanks to Rose Jackson and 
 * the OSCDL project.
 *
 * @contributors  Eric Hanson, Morgan Harvey, Cristopher Holm, 
 *                David Percy, Tim Welch
 *
 * Copyright (c) 2006, Academic & Research Computing,
 * Portland State University, Portland Oregon.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 * 
 *    * Redistributions in binary form must reproduce the above
 *      copyright notice, this list of conditions and the following
 *      disclaimer in the documentation and/or other materials provided
 *      with the distribution.
 *
 *    * Neither the names of Academic and Research Computing or Portland
 *      State University nor the names of their contributors may be used
 *      to endorse or promote products derived from this software
 *      without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 ***********************************************************************/ 

/****************************************************************************
 * This file contains the functionality to read a mapserver map file,
 * create an image from that map file and return everything as
 * well-formed XML. This file is intended to be called via AJAX, so
 * all results are formatted as <id>/<value> tag pairs.
 ***************************************************************************/

header("Content-type: text/plain charset=utf-8");

if (!extension_loaded("MapScript")) {
	dl("php_mapscript.so");
}

require_once(".root_config.php");
require_once($ROOT."config.php");
require_once("DataContainer.php");
require_once("JSON.php");

$container=&new DataContainer();
$json=&new Services_JSON();

/*************** delete old images ******************/

$old_map_image = isset($_REQUEST['old_map_image']) ? $_REQUEST['old_map_image'] : "";
$old_refmap_image = isset($_REQUEST['old_refmap_image']) ? $_REQUEST['old_refmap_image'] : "";
$old_scale_image = isset($_REQUEST['old_scale_image']) ? $_REQUEST['old_scale_image'] : "";

if ($old_map_image && file_exists($ROOT.$old_map_image)) {
  unlink($ROOT.$old_map_image);
}

if ($old_refmap_image && file_exists($ROOT.$old_refmap_image)) {
  unlink($ROOT.$old_refmap_image);
}

if ($old_scale_image && file_exists($ROOT.$old_scale_image)) {
  unlink($ROOT.$old_scale_image);
}

/********************* required query string parameters ******************/
if (isset($_REQUEST['mapfile']))
  $mapfile=$_REQUEST['mapfile'];
else
  $container->errors[]="No mapfile";

if (isset($_REQUEST['session_id']))
  $session_id=$_REQUEST['session_id'];
else
  $container->errors[]="No session ID";

if (count($container->errors)>0) {
  print $json->encode($container);
  exit(0);
}

$cwd=getcwd()."/";

/**************************** map object ***********************************/
//if ($mapfile=="US_mapfile.map" || $mapfile=="World_mapfile.map") {
  $oMap=ms_newMapObj($ROOT."$map_path$mapfile",$ROOT);
//} else {
//  $oMap=ms_newMapObj($ROOT."$map_path$mapfile");
//}

/**************************** layers **************************************/
if (isset($_REQUEST['layers']))
	$layers=$_REQUEST['layers'];
else
  $layers=array();

// for each layer, if it's on in $layers, set status = on, else set it to off
$layer_count=count($layers);
$groups=array();
$layer_names=array();
$layer_status=array();
for ($i=0; $i<$oMap->numlayers; ++$i) {
  $oLayer=$oMap->getLayer($i);
  if ($layer_count>0) {
    if (in_array($oLayer->name,$layers)) {
      $oLayer->set("status",MS_ON);
    } else {
      $oLayer->set("status",MS_OFF);
    }
  }
  $groups[]=$oLayer->group;
  $layer_names[]=$oLayer->name;
  $is_on=($oLayer->status==MS_ON || $oLayer->status==MS_DEFAULT);
  $layer_status[]=($is_on)?"on":"off";
}

if (count($groups)>0)
  $container->groups=$groups;
if (count($layer_names)>0)
  $container->layers=$layer_names;
if (count($layer_status)>0)
  $container->layer_status=$layer_status;

/*************** image_width & image_height **********************************/
if (!empty($_REQUEST['image_width']) && !empty($_REQUEST['image_height'])) {
  $image_width=$_REQUEST['image_width'];
  $image_height=$_REQUEST['image_height'];
  $oMap->setSize($image_width,$image_height);
} else {
  // default image width and height
  $image_width=$oMap->width;
  $image_height=$oMap->height;
}

/**************************** extent ****************************************/
if (isset($_REQUEST['extent'])) {
	$extent = $_REQUEST['extent'];

	// check to see that these values make sense (aka they're non-zero) 
	if ($extent['x1'] && $extent['y1'] &&
	    $extent['x2'] && $extent['y2']) {
		// set the extent
		$oMap->setExtent($extent['x1'], $extent['y1'], $extent['x2'], $extent['y2']);

		// set up extent object for use in zoom (!?!?)
		$oExtent = ms_newRectObj();
		$oExtent->setExtent($extent['x1'], $extent['y1'], $extent['x2'], $extent['y2']);
	} else {
		$oExtent = ms_newRectObj();
		$oExtent->setExtent ($oMap->extent->minx, $oMap->extent->miny, $oMap->extent->maxx, $oMap->extent->maxy);
	}
} else {
	$oExtent = ms_newRectObj();
	$oExtent->setExtent ($oMap->extent->minx, $oMap->extent->miny, $oMap->extent->maxx, $oMap->extent->maxy);
}

/************************** max extents **************************************/
$oMaxExtent=ms_newRectObj();
if (isset($_REQUEST['max_extent'])) {
  $max_extent=$_REQUEST['max_extent'];
  $oMaxExtent->setextent($max_extent['x1'], $max_extent['y1'], $max_extent['x2'], $max_extent['y2']);
} else {
	$oMaxExtent->setextent($oMap->extent->minx, $oMap->extent->miny, $oMap->extent->maxx, $oMap->extent->maxy);
}

$projection=$oMap->getProjection();
$isProjection=($projection && strlen($projection)>0);

/**************************** zoom ****************************************/
if (isset($_REQUEST['zoom'])) {

  $zoom=$_REQUEST['zoom'];
  
  if (isset($zoom['scale'])) {

      if (isset($zoom['x1']) && isset($zoom['y1'])) {
          //Get and set point to zoom to
          $x = $zoom['x1'];
          $y = $zoom['y1'];
          $oPointCenter=ms_newPointObj();
          $oPointCenter->setXY($x,$y);

          // project the point if necessary
          if ($isProjection) {
              $oProjIn = ms_newProjectionObj("proj=longlat");
              $oProjOut = ms_newProjectionObj($projection);
              $oPointCenter->project($oProjIn, $oProjOut);
          } elseif ($oMap->units!=MS_DD) {
              $container->errors[]="Unable to zoom: No projection is defined and ".
                  "the map units are not in decimal degrees";
          }
          
          //Create new map extent around the point to zoom to.  It doesn't
          //need to be large.  
          $box_radius = .01;
          $minx=$oPointCenter->x-$box_radius;
          $miny=$oPointCenter->y-$box_radius;
          $maxx=$oPointCenter->x+$box_radius;
          $maxy=$oPointCenter->y+$box_radius;
          $oExtent->setExtent($minx,$miny,$maxx,$maxy);
          
          //Verify point is within new extent
          $point_intersects = isIntersecting($oPointCenter,$oExtent);

          if ($point_intersects) {

              //Create new layer/class/style for geocoded point
              $oGeocodeLayer = ms_newLayerObj($oMap);

              if (!$oGeocodeLayer) {
                  $container->errors[]="Geocode layer $geocode_result_layer unconfigured, or doesn't exist in current map file";
                  return;
              }

              $oGeocodeLayer->set("name", "GeocodeLayer");
              $oGeocodeLayer->set("type", MS_LAYER_POINT);
              $oGeocodeLayer->set("status",MS_ON);

              $oGeocodeClass = ms_newClassObj($oGeocodeLayer);
              $oGeocodeClass->set("name", "Found Address");

              $oGeocodeStyle = ms_newStyleObj($oGeocodeClass);
              $oGeocodeStyle->set("symbol", $oMap->getsymbolbyname("Circle"));
              $oGeocodeStyle->set("size", 8);
              $oGeocodeStyle->set("minsize", 10);

              $oGeocodeStyle->color->setRGB(255, 0, 0);
              $oGeocodeStyle->outlinecolor->setRGB(0, 0, 0);

              // get the layer for the point of interest
              //$oPointLayer=$oMap->getLayerByName($geocode_result_layer);
              
              if($isProjection)
                  $oGeocodeLayer->setProjection($projection);
              // create a shape for the point of interest
              $oShape=ms_newShapeObj(MS_SHAPE_POINT);
              // and make a line... 
              $oLine=ms_newLineObj();
              $oLine->add($oPointCenter);
              // and add it to the shape
              $oShape->add($oLine);
              $oGeocodeLayer->addFeature($oShape);

              //Use center of image such that it zooms to center of extent we defined
              $oPointZoom=ms_newPointObj(); 
              $oPointZoom->setXY($image_width/2,$image_height/2);

              //$oMap->zoompoint(1,$oPointZoom,$image_width,$image_height,$oExtent);
              $oMap->zoomscale($zoom['scale'],$oPointZoom,$image_width,$image_height,$oExtent,$oMaxExtent);
          }

      } else {
          $oPoint=ms_newPointObj();
          $midx=$image_width/2;
          $midy=$image_height/2;
          $oPoint->setXY($midx,$midy);
          $oMap->zoomscale($zoom['scale'],$oPoint,$image_width,$image_height,$oMap->extent,$oMaxExtent);
          if($oMap->scale < $min_map_scale) {
              $midx=$image_width/2;
              $midy=$image_height/2;
              $oPoint->setXY($midx,$midy);
              $oMap->zoomscale($min_map_scale,$oPoint,$image_width,$image_height,$oMap->extent,$oMaxExtent); 
          }
      }

  } elseif (isset($zoom['factor']) && isset($zoom['x1']) && isset($zoom['y1']) &&
      $zoom['x1'] && $zoom['y1']) { // non-zero?
    $zoomfactor=$zoom['factor'];
    $oPoint=ms_newPointObj();
    $oPoint->setXY($zoom['x1'],$zoom['y1']);
    $oMap->zoompoint($zoomfactor,$oPoint,$oMap->width,$oMap->height,$oExtent,$oMaxExtent);
    if($oMap->scale < $min_map_scale) {
      $oPoint=ms_newPointObj();
      $midx=$image_width/2;
      $midy=$image_height/2;
      $oPoint->setXY($midx,$midy);
      $oMap->zoomscale($min_map_scale,$oPoint,$image_width,$image_height,$oMap->extent,$oMaxExtent); 
    }

  } elseif (isset($zoom['x1']) && isset($zoom['y1']) && isset($zoom['x2']) && isset($zoom['y2'])) {
    if (isset($_REQUEST['offset'])) {
      // x1 and y1 are the original point, so use x2 and y2 to calculate the
      // offset
      $x1=$zoom['x1'];
      $y1=$zoom['y1'];
      $x2=$zoom['x2'];
      $y2=$zoom['y2'];
      $offset_x=$x2-$x1;
      $offset_y=$y2-$y1;
      $point_x=$image_width/2 - $offset_x;
      $point_y=$image_height/2 - $offset_y;
      $oPoint=ms_newPointObj();
      $oPoint->setXY($point_x,$point_y);
      $oMap->zoompoint(1,$oPoint,$oMap->width,$oMap->height,$oExtent,$oMaxExtent);
    } elseif (isset($zoom['dd'])) {
      // specifies that the zooming should be to a specific lat/long given in
      // decimal degrees. x1 = longitude west, y1 = latitude south, 
      // x2 = longitude east and y2 = latitude north.
      $oZoomRect=ms_newRectObj();
      $oZoomRect->setextent($zoom['x1'],$zoom['y1'],$zoom['x2'],$zoom['y2']);
      $oZoomRect->fit($image_width,$image_height);
      if ($isProjection) {
        $oProjIn = ms_newProjectionObj("proj=longlat");
        $oProjOut = ms_newProjectionObj($projection);
        $oZoomRect->project($oProjIn, $oProjOut);
      } elseif ($oMap->units!=MS_DD) {
        // this is a bit of an error. there's no projection defined, and the
        // units aren't in decimal degrees, so what is a map to do? throw an
        // error, that's what! (silly user)
        $container->errors[]="Unable to zoom: No projection is defined and ".
          "the map units are not in decimal degrees";
      }
      
      // apply the zoom selection to the previous extent
      $oMap->zoomrectangle($oZoomRect,$image_width,$image_height,$oExtent,$oMaxExtent);
    } else {
      /*************************************************************************
       * mapserver expects bounding box extents to be in the form of 2 points
       * whose coorinates describe the lower left point and upper right point as
       * minx, miny and maxx, maxy respectively.
       *  (x1,y1)
       *     ------- UR (values for maxx, maxy)
       *     |     |
       *     |     |
       *     |     |
       *     ------- (x2,y2)
       *     LL (values for minx, miny)
       * 
       * these coordinates are expressed as pixel coordinates within the map 
       * image, with (0,0) being the upper left corner. 
       * (the maxy in the rect object should be < miny value)
       ************************************************************************/
      // set x1 to the left most x value
      // and x2 to the right most x value
      if ($zoom['x1']<=$zoom['x2']) {
        $x1=$zoom['x1'];
        $x2=$zoom['x2'];
      } else {
        $x2=$zoom['x1'];
        $x1=$zoom['x2'];
      }
      // set y1 to the top most y value
      // and y2 to the bottom most y value
      if ($zoom['y1']<=$zoom['y2']) {
        $y1=$zoom['y1'];
        $y2=$zoom['y2'];
      } else {
        $y2=$zoom['y1'];
        $y1=$zoom['y2'];
      }
      
      // set up a zoom rect for the selected region
      $oZoomRect=ms_newRectObj();
      // setextent(double minx, double miny, double maxx, double maxy)
      $oZoomRect->setextent($x1,$y2,$x2,$y1);
      $oZoomRect->fit($image_width,$image_height);
      // apply the zoom selection to the previous extent
      $oMap->zoomrectangle($oZoomRect,$image_width,$image_height,$oExtent,$oMaxExtent);
    }
        
    if($oMap->scale < $min_map_scale ) {
      $oPoint=ms_newPointObj();
      $midx=$image_width/2;
      $midy=$image_height/2;
      $oPoint->setXY($midx,$midy);
      $oMap->zoomscale($min_map_scale,$oPoint,$image_width,$image_height,$oMap->extent,$oMaxExtent); 
    }
	}
}

/***************************************************************************
 * isIntersecting determines if a point falls withing a rectangle
 * polygon. it
 * assumes that all parameters are already in the proper
 * projection. returns
 * true or false.
 ***************************************************************************/
function isIntersecting(&$oPoint,&$oExtents) {
    // create a bounding box based on the extents
    $oLine=ms_newLineObj();
    $oLine->addXY($oExtents->minx,$oExtents->maxy);
    $oLine->addXY($oExtents->maxx,$oExtents->maxy);
    $oLine->addXY($oExtents->maxx,$oExtents->miny);
    $oLine->addXY($oExtents->minx,$oExtents->miny);
    $oLine->addXY($oExtents->minx,$oExtents->maxy);
    // create a polygon based on these line segments
    $oPolygon=ms_newShapeObj(MS_SHAPE_POLYGON);
    $oPolygon->add($oLine);
    return $oPolygon->contains($oPoint);
}


/********************************* scalebar *********************************/
$oMap->scalebar->set("status",MS_ON);
$oMap->scalebar->backgroundcolor->setRGB(200,200,255);
$oMap->scalebar->setimagecolor(255, 255, 255);
$oMap->scalebar->outlinecolor->setRGB(100, 150, 200);
$oMap->scalebar->label->set("type",MS_BITMAP);
$oMap->scalebar->label->set("minsize",7);
$oMap->scalebar->label->set("maxsize",10);
$oMap->scalebar->set("transparent",MS_TRUE);
$oMap->scalebar->set("width", 280);
$oMap->scalebar->set("height", 5);
$oMap->scalebar->set("intervals", 6);
$oMap->scalebar->set("style", 0);

/********************************* map image *********************************/
$image=$oMap->draw();
$map_img=$map_image_path.$mapfile.".".$session_id.".".rand(100,100000).".png";
$image->saveImage($ROOT.$map_img);

//Set path to image from web root (should not use $ROOT)
$container->map_image=$map_img;

/*************************** map state information ***************************/
$container->minx=($oMap->extent->minx);
$container->miny=($oMap->extent->miny);
$container->maxx=($oMap->extent->maxx);
$container->maxy=($oMap->extent->maxy);
$container->scale=round($oMap->scale);
$container->units=getUnits($oMap->units);

//bug request 1607285
//If not decimal degree then round to the nearest integer
if ($container->units != 'Decimal Degrees') {
    $container->minx = round($container->minx);
    $container->miny = round($container->miny);
    $container->maxx = round($container->maxx);
    $container->maxy = round($container->maxy);
}

$to_dms=(isset($_REQUEST['dms']))?$_REQUEST['dms']:$dms;
$dd_precision=(isset($_REQUEST['dd_precision']))?$_REQUEST['dd_precision']:$dd_precision;

if ($isProjection || $oMap->units==MS_DD) {
  if ($isProjection) {
    $oProjIn = ms_newProjectionObj($projection);
    $oProjOut = ms_newProjectionObj("proj=longlat");
  }
  
  $oPoint = ms_newPointObj();
  $oPoint->setXY($oMap->extent->minx, $oMap->extent->miny);
  if ($isProjection) $oPoint->project($oProjIn, $oProjOut);
  if ($to_dms) {
    $bottom_lat=dd_to_dms($oPoint->y);
    $left_lon=dd_to_dms($oPoint->x);
  } else {
    $bottom_lat = round($oPoint->y,$dd_precision);
    $left_lon = round($oPoint->x,$dd_precision);
  }
  
  $oPoint->setXY($oMap->extent->maxx, $oMap->extent->maxy);
  if ($isProjection) $oPoint->project($oProjIn, $oProjOut);
  if ($to_dms) {
    $top_lat = dd_to_dms($oPoint->y);
    $right_lon = dd_to_dms($oPoint->x);
  } else {
    $top_lat = round($oPoint->y,$dd_precision);
    $right_lon = round($oPoint->x,$dd_precision);
  }
  
  $midx = $oMap->extent->minx + ($oMap->extent->maxx - $oMap->extent->minx)/2;
  $midy = $oMap->extent->miny + ($oMap->extent->maxy - $oMap->extent->miny)/2;
  $oPoint->setXY($midx, $midy);
  if ($isProjection) $oPoint->project($oProjIn, $oProjOut);
  if ($to_dms) {
    $mid_lat = dd_to_dms($oPoint->y);
    $mid_lon = dd_to_dms($oPoint->x);
  } else {
    $mid_lat = round($oPoint->y,$dd_precision);
    $mid_lon = round($oPoint->x,$dd_precision);
  }
  $container->latitude_south=$bottom_lat;
  $container->latitude_north=$top_lat;
  $container->longitude_east=$right_lon;
  $container->longitude_west=$left_lon;
  $container->latitude_mid=$mid_lat;
  $container->longitude_mid=$mid_lon;
}

if ($isProjection) {
  $proj=parseProjection($projection);
  $container->projection=$proj['projection'];
  $container->ellipsoid=$proj['ellipsoid'];
  $container->datum=$proj['datum'];
}

/****************************** reference map ******************************/
if ($oMap->reference && strlen($oMap->reference->image)>0) {
  $image=$oMap->drawReferenceMap();
  $refmap_img=$map_image_path.$mapfile.".".$session_id.".".rand(100,100000).".refmap.png";
  $image->saveImage($ROOT.$refmap_img);
  
  $container->refmap_image=$refmap_img;
}

/****************************** legend ******************************/

if ($oMap->legend && strlen($oMap->legend->status == MS_ON)) {
    $legend_html = $oMap->processlegendtemplate(null);
    $container->template = $oMap->processlegendtemplate(null);;
    $container->legend_html = $legend_html;
}

/****************************** scalebar image ******************************/
if ($oMap->units==MS_INCHES || $oMap->units==MS_FEET || $oMap->units==MS_MILES) {
  if ($oMap->scale<2000)
    $oMap->scalebar->set("units", MS_FEET);
  else
    $oMap->scalebar->set("units", MS_MILES);
} elseif ($oMap->units==MS_METERS || $oMap->units==MS_KILOMETERS) {
  if ($oMap->scale<2000)
    $oMap->scalebar->set("units", MS_METERS);
  else
    $oMap->scalebar->set("units", MS_KILOMETERS);
}
$image=$oMap->drawScaleBar();
$scalebar_img=$map_image_path.$mapfile.".".$session_id.".".rand(100,100000).".scalebar.png";
$image->saveImage($ROOT.$scalebar_img);

$container->scalebar_image=$scalebar_img;

/************************ send output as plain text ************************/
print $json->encode($container);

/**************************************************************************
 * parseProjection()
 * parses the projection string provided by the map object (as found in the
 * mapfile) and returns an array of human-readable (hopefully) projection
 * information. to do this it uses the highly unwieldy PROJ.4 library (which
 * is also what mapserver uses to manage projections). for more information on
 * PROJ.4, see http://www.remotesensing.org/proj/. 
 *************************************************************************/
function parseProjection($projection) {
  $proj=array('projection'=>'Unknown','datum'=>'Unknown','ellipsoid'=>'Unknown');
  $temp=explode("+",$projection);
  foreach ($temp as $value) {
    $x=explode("=",$value);
    if ($x[0]=="proj" || $x[0]=="init") {
      $p=trim($x[1]);
      if (isset($_ENV['LD_LIBRARY_PATH'])) {
        // look for proj4 in the environment library paths 
        $libpaths=explode(":",$_ENV['LD_LIBRARY_PATH']);
        foreach ($libpaths as $libpath) {
          if (strpos($libpath,"/mapserver/proj4")!==false) {
            // if found, the proj executable should be in the related bin
            // directory
            $b=explode("/",$libpath);
            $projpath=NULL;
            for ($i=0;$i<count($b);++$i) {
              if ($b[$i]=="lib") {
                $b[$i]="bin";
                $projpath=implode("/",$b);
                break;
              }
            }
            if ($projpath) {
              $proj_str=array();
              exec($projpath."/proj -l=".$p,$proj_str);
              if (count($proj_str)>0) {
                $p=explode(":",$proj_str[0]);
                $proj['projection']=trim($p[1]);
              }
            }
          }
        }
      }
    } elseif ($x[0]=="ellps") {
      $proj['ellipsoid']=trim($x[1]);
    } elseif ($x[0]=="datum") {
      $proj['datum']=trim($x[1]);
    }
  }
  return $proj;
}

/***************************************************************************
* dd_to_dms: converts decimal degrees to degrees/minutes/seconds
* *************************************************************************/
function dd_to_dms($deg) {
	$deg_d=intval($deg);
	$deg=60*($deg-$deg_d);
	$deg_m=intval($deg);
	$deg_s=60*($deg-$deg_m);
	return $deg_d."&#176; ".abs($deg_m)."&#8217; ".abs(round($deg_s))."&#8221;";
}

/**************************************************************************
 * returns a string represent the map's units type based on the MapScript
 * constant passed to it.
 *************************************************************************/
function getUnits($units_const) {
  switch ($units_const) {
    case MS_INCHES: return "Inches";
    case MS_FEET: return "Feet";
    case MS_MILES: return "Miles";
    case MS_METERS: return "Meters";
    case MS_KILOMETERS: return "Kilometers";
    case MS_DD: return "Decimal Degrees";
    case MS_PIXELS: return "Pixels";
    default: return "Unknown";
  }
}
?>
