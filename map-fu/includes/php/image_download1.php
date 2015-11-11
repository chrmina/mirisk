<?php
 /***********************************************************************
 *
 * @file          image_download1.php
 *
 * $Id: image_download1.php 95 2006-12-22 17:08:27Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/image_download1.php $
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

require_once(".root_config.php");
require_once($ROOT."config.php");

if (strlen($_SERVER['QUERY_STRING'])>0) {
  $url=$_SERVER['QUERY_STRING'];
} else {
  die("Invalid query string");
}
if (isset($_REQUEST['mapfile'])) {
  $mapfile=$_REQUEST['mapfile'];
} else {
  die("mapfile not defined");
}
if (isset($_REQUEST['image_width'])) {
  $image_width=$_REQUEST['image_width'];
} else {
  die("Image width not defined");
}
if (isset($_REQUEST['image_height'])) {
  $image_height=$_REQUEST['image_height'];
} else {
  die("Image height not defined");
}
switch ($mapfile) {
case "US_mapfile.map":
//  $legend="";
  break;
case "World_mapfile.map":
//  $legend="";
  break;
default:
  die("Invalid map file");
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Oregon Geologic Data Compilation</title>
<style type="text/css">
#download_background {
	position: absolute;
	display:block;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	z-index:100;
}

#download_window {
	position:absolute;
	top:0%;
	height:90%;
	left:5%;
	width:90%;
	z-index:901;
/*	border:1px dashed #bbb; */
	padding: 2em;
	overflow: auto;
	font-size: 80%;
    font-family: trebuchet, arial, sans-serif;
}
    
    
    
    

}

#download_window h4 {
	font-size:120%;
}

#download_window .label {
/*	font-size:12px; */
	font-weight:700;
	padding-left:30px;
}

#download_window .option {
/*	font-size:12px;  */
	font-weight:100;
	padding-left:7px;
}

#download_window .opt_group {
	margin-left:30px;
}

#download_window .download_button {
/*	font-size:12px;  */
/*	font-weight:600;  */
}
</style>
<script type="text/javascript" language="Javascript">
var url='<?php echo $url; ?>';
function getRadioVal(stub) {
  var radio;
	for (var i=0;i<100;++i){
		radio = document.getElementById(stub + "_" + i);
		if (radio) {
      if (radio.checked) return radio.value;
		} else {
		  return null;
		}
	}
}
function createImage() {
  if (document.getElementById("image_type_0").checked) {
    var print_size = getRadioVal("print_size");
    var resolution = getRadioVal("resolution");
    //var format = getRadioVal("format");
    if (print_size && resolution)
      location.href="image_download2.php?"+url+"&print_size="+print_size+"&resolution="+resolution;
    else {
      var msg="Error determining:\n";
      if (!print_size)
        msg+="print size\n";
      if (!resolution)
        msg+="resolution\n";
      alert(msg);
    }
  } else {
    //Direct to backup static images
    var params=url.split("&");
    var param;
    for (var i=0;i<params.length;++i) {
      param=params[i].split("=");
      if (param[0]=="mapfile") {
        switch (param[1]) {
        case "US_mapfile.map":
          location.href="us.png";
          break;
        case "World_mapfile.map":
          location.href="world.png";
          break;
        }
        break;
      }
    }
  }
}
function switchType(image_type) {
  var enable=(image_type=='map');
  var radio;
  for (var i=0;i<4;++i) {
    radio = document.getElementById("print_size_" + i);
    radio.disabled=!enable;
  }
  for (var i=0;i<4;++i) {
    radio = document.getElementById("resolution_" + i);
    radio.disabled=!enable;
  }
}
</script>
</head>
<body>
<div id="download_background">
	<div id="download_window"><p><strong>Instructions:</strong> You can download and save the legend for the currently selected theme or you can download and save, with an image size and resolution you select, the current map extent displayed on the screen. The format of the saved file is .PNG. To print the saved file, open the .PNG file in your browser window or another application with printing capabilities and print from that application.</p>

	<h4><input type="radio" name="image_type" id="image_type_1" value="legend" onclick="switchType('legend');"/>Download Current Theme Legend Image</h4>
	    
	    <p>- OR -</p>

		<h4><input type="radio" name="image_type" id="image_type_0" value="map" checked="checked" onclick="switchType('map');"/>Download Current Map Image</h4>
		<span class="label">Select Image Size:</span><br/>
				<div class="opt_group">
			<input type="radio" name="print_size" id="print_size_0" value="640x480" checked="checked" /><span class="option">640 x 480 pixels</span><br/>

			<input type="radio" name="print_size" id="print_size_1" value="1024x768" /><span class="option">1024 x 768 pixels</span><br/>
			<input type="radio" name="print_size" id="print_size_2" value="3000x2000" /><span class="option">3000 x 2000 pixels</span><br/>
			<input type="radio" name="print_size" id="print_size_3" value="letter" /><span class="option">letter (8.5 x 11 inches)</span><br/>
		<br/></div>
				<span class="label">Select Image Resolution:</span><br/>
		<div class="opt_group">
			<input type="radio" name="resolution" id="resolution_0" value="72" checked="checked" /><span class="option">72 ppi (screen)</span><br/>

			<input type="radio" name="resolution" id="resolution_1" value="300" /><span class="option">300 ppi</span><br/>
			<input type="radio" name="resolution" id="resolution_2" value="600" /><span class="option">600 ppi</span><br/>
			<input type="radio" name="resolution" id="resolution_3" value="1200" /><span class="option">1200 ppi</span><br/>
		</div><br/>
    <input class="download_button" type="button" name="do_print" id="do_print" value="Create Printable Image File" onclick="createImage();"/>
	</div>
</div>
</body>

</html>
