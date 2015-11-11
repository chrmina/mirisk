<?
 /***********************************************************************
 *
 * @file          image_download2.php
 *
 * $Id: image_download2.php 95 2006-12-22 17:08:27Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/image_download2.php $
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

if (!extension_loaded("MapScript")) 
	dl("php_mapscript.so");

require_once(".root_config.php");
require_once($ROOT."config.php");

$err=NULL;

/**************************** layers ****************************************/
if (isset($_REQUEST['layers']))
	$layers=$_REQUEST['layers'];
else
  $layers=array();

if (isset($_REQUEST['mapfile'])) {
  $mapfile=$_REQUEST['mapfile'];
} else {
  $err="No mapfile";
}

if (isset($_REQUEST['session_id'])) {
  $session_id=$_REQUEST['session_id'];
} else {
  if ($err)
    $err.="<br/>No session ID";
  else
    $err="No session ID";
}

if ($err) {
  die($err);
} else {
  $map=ms_newMapObj($ROOT.$map_path.$mapfile, $ROOT);
}

// for each layer, if it's on in $layers, set status = on
// else set it to off
if (count($layers)>0) {
  for ($i=0; $i<$map->numlayers; ++$i) {
    $oLayer=$map->getLayer($i);
    if (in_array($oLayer->name,$layers)) {
      $oLayer->set("status",MS_ON);
    } else {
      $oLayer->set("status",MS_OFF);
    }
  }
}

switch ($_REQUEST['print_size']){
	case "640x480":
		$iwidth = 640;
		$iheight = 480;
	break;
	case "1024x768":
		$iwidth = 1024;
		$iheight = 768;
	break;

	case "3000x2000":
		$iwidth = 3000;
		$iheight = 2000;
	break;
	
	case "letter":
		$iwidth = 11 * $_REQUEST["resolution"];
		$iheight = 8.5 * $_REQUEST["resolution"];
	break;

	default:
		$iwidth = 640;
		$iheight = 480;
	break;
}

$map->set ("width", $iwidth);
$map->set ("height", $iheight);

$format='png';
/*
$format = isset($_REQUEST['format'])?$_REQUEST['format']:'png';
switch ($format){
	case "png":
		$map->selectOutputFormat("PNG24");
	break;

	case "gif":
		$map->selectOutputFormat("GIF");
	break;

	case "jpeg":
		$map->selectOutputFormat("JPEG");
	break;

	case "tiff":
		$map->selectOutputFormat("GTIFF");
	break;

	case "pdf":
		$map->selectOutputFormat("pdf");
	break;
	
	default:
		$map->selectOutputFormat("PNG24");
	break;
}
*/

//**************************** extent ****************************************
if (isset($_REQUEST['extent'])) {
	$extent = $_REQUEST['extent'];

	// check to see that these values make sense (aka they're non-zero) 
	if ($extent['x1'] && $extent['y1'] &&
	    $extent['x2'] && $extent['y2']) {
		// set the extent
		$map->setExtent($extent['x1'], $extent['y1'], $extent['x2'], $extent['y2']);
	}
}

// draw the image and pass it thru
header ("Cache-Control: max-age=600");
header ("Content-disposition: attachment; filename=\"Map-Fu_image." . $format . "\"");
if ($format == "pdf") {
	header ("Content-type: application/x-pdf");
} else {
	header ("Content-type: image/" . $format);
}
//print_r($extent);
//print_r ($map->extent);
// blank param means send to stdout
$image = $map->draw();

$image->saveImage ("", $map);
?>
