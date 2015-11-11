<?php
 /***********************************************************************
 * @file          ZoomTo.php
 *
 * $Id: ZoomTo.php 107 2007-02-17 18:57:22Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/ZoomTo.php $
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
 *                David Percy
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
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 ***********************************************************************/ 

/****************************************************************************
 * This file contains the class ZoomTo. It is intended to provide the look
 * and functionality of an interface tool that allows the user to zoom to a
 * specific scale or set of coordinates on the existing map.
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/
require_once($abstract_path."Tool.php");

class ZoomTo extends Tool {
  
  function __construct(&$group) {
    global $image_path;
    $this->name="Zoom To";
    $this->id="zoom_to";
    $this->description="Use this tool to zoom to a specific scale, a set of coordinates or an address";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_zoomto.png";
    $this->type=CHANGE_EXTENT_TOOL;
    $this->clickHandler="ZoomToClick";
    $this->selectionHandler="ZoomToHandler";
    $this->lostFocusHandler="ZoomToUnfocus";
    $this->showZoomToScale = true;
    $this->showZoomToCoordinates = true;
    $this->showZoomToAddress = true;
  }
  
  function __destruct() {
    //
  }
  
  public function inlineCSS() {
    $css="#".$this->id."_panel {background-color:#fff;}\n";
    return $css;
  }
  
  public function inlineJavascript() {
    global $php_path;
    global $geocode_service;

    $js=<<<EOT

function {$this->clickHandler}(evt) {
  panels.onToolClick('{$this->id}');
  showZoomToPanel(evt);
}

function {$this->selectionHandler}() {
  var errs=new Array();
  var type = "";
  var scale, lat_north, lat_south, lon_west, lon_east, address, city, state, zip;

  var scale_input = document.getElementById("zoom_to_scale"); 
  var lat_north_input = document.getElementById("lat_north");
  var lat_south_input = document.getElementById("lat_south");
  var lon_west_input = document.getElementById("lon_west");
  var lon_east_input = document.getElementById("lon_east");
  var address_input = document.getElementById("zoom_to_address");
  var city_input = document.getElementById("zoom_to_city");
  var state_input = document.getElementById("zoom_to_state");
  var zip_input = document.getElementById("zoom_to_zip");

   //If zoom to scale
  if (scale_input && scale_input.value) {
    scale = scale_input.value;
    if (scale.length>0) {      
      //var clean_scale=scale.replace(/\D/gi,"");
      if (isNaN(scale)) {
        error.print("Invalid scale "+scale+". The scale must be an integer with no commas.");
        return;
      } else {
        map.updateMap("zoom[scale]="+scale,true);
        return;
      }
    }
  } 

  //If zoom to coordinates
  else if ((lat_north_input && lat_north_input.value) || 
           (lat_south_input && lat_south_input.value) || 
           (lon_west_input && lon_west_input.value) || 
           (lon_east_input && lon_east_input.value)) {

      //Check all input provided
      if (!lat_north_input.value) {
          errs.push("Latitude North not provided");
      } else {
          lat_north=parseFloat(lat_north_input.value);
      }
      if (!lat_south_input.value) {
          errs.push("Latitude South not provided");
      } else {
          var lat_south=parseFloat(lat_south_input.value);
      }
      if (!lon_west_input.value) {
          errs.push("Latitude West not provided");
      } else {
          var lon_west=parseFloat(lon_west_input.value);
      }
      if (!lon_east_input.value) {
          errs.push("Latitude East not provided");
      } else {
          var lon_east=parseFloat(lon_east_input.value);
      }

      if (errs.length > 0) {
          //There were errors
          error.print(errs.join("\\n"));
          return;
      }

      //Check they are numbers
      if (isNaN(lat_north)) errs.push("Latitude North must be a number");
      if (isNaN(lat_south)) errs.push("Latitude South must be a number");
      if (isNaN(lon_west)) errs.push("Longitude West must be a number");
      if (isNaN(lon_east)) errs.push("Longitude East must be a number");
      
      if (errs.length>0) {
          // there were errors
          error.print(errs.join("\\n"));
          return;
      } 

      //Check the numbers make sense
      if (lat_north<lat_south) errs.push("Latitude North must be greater than Latitude South");
      if (lon_east<lon_west) errs.push("Longitude East must be greater than Longitude West");

      if (errs.length>0) {
          // there were errors
          error.print(errs.join("\\n"));
          return;
      }

      //If necessary, change coordinates to be within the bounds of
      //the current map
      if (lat_north>map.maxLat) lat_north=map.maxLat;
      if (lat_south<map.minLat) lat_south=map.minLat;
      if (lon_west<map.minLon) lon_west=map.minLon;
      if (lon_east>map.maxLon) lon_east=map.maxLon;

      //Generate query string
      var qs="zoom[x1]="+lon_west+"&zoom[x2]="+lon_east+
          "&zoom[y1]="+lat_south+"&zoom[y2]="+lat_north+"&zoom[dd]=1";
      map.updateMap(qs,true);
  }
  
  //If zoom to address
  else if (address_input && address_input.value) {
      if (!geocoderClient) {
          error.print("geocoder not configured");
          return;
      }

      address = address_input.value;
      
      //Start geocode request
      geocoderClient.geocode(process_zoom_to_address, address);

  } else {
      error.print("No input provided in ZoomTo window");
  }
  zoomToFloater.hide();
}

/****************************************************************************
 * process_zoom_to_address(GeocoderResult geoResult)
 *
 * Callback function for geocode request.  
 ***************************************************************************/
function process_zoom_to_address(geoResult) {

    var status, statusStr, num_results, address, 
        accuracy, accuracyStr, lat, lng, location, output = null;

    if (!geoResult) {
        error.print("GeocoderResult not returned", "ZoomTo");
        return;
    }

    status = geoResult.getStatus();
    statusStr = geoResult.getStatusStr();

    if (!geoResult.getSuccess()) {
        var msg = "Returned no result";
        if (status)
            msg += " (" + status + ": " + statusStr + ")";
        error.print(msg, "ZoomTo");
        return;
    }

    num_results = geoResult.locations.length;

    if (num_results == 1) {        
        output = "<center>Geocoder returned:</center><br>";
/*
        //Retrieve the location data
        location = geoResult.getLocation(0);
        address = location.getAddress();
        accuracy = location.getAccuracy();
        accuracyStr = location.getAccuracyStr();
        lat = location.getLat();
        lng = location.getLng();
        radius = location.getRadius();

        //Display the location
        error.print(location.toHTML(), "Geocoder returned");

        //Zoom to the location
        var qs="zoom[x1]="+lng+"&zoom[y1]="+lat+"&zoom[point]=1"+"&zoom[radius]="+radius;
        map.updateMap(qs,true);
*/
    } else {
        //Build output for user selection
        output = "<center>Geocoder returned multiple results:</center><br>";

    }    

    for (var j=0; j<geoResult.locations.length; j++) {
        var location = geoResult.getLocation(j);
        var lat = location.getLat();
        var lng = location.getLng();
        var scale = location.getScale();
        var qs = "zoom[x1]="+lng+"&zoom[y1]="+lat+"&zoom[scale]="+scale;
        var js = "hideAlert(); map.updateMap(\"" + qs + "\", true);";
        var link = "<a href='javascript:void(0);' onclick='" + js + "'>Zoom To</a>";
        output += (geoResult.getLocation(j)).toHTML() + "<br>" + link + "<br><br>";
    }

    //Display the selections
    showAlert(output);
}

function {$this->lostFocusHandler}() {
  zoomToFloater.hide();
}
var zoomToLeft=zoomToTop=INVALID;
var zoomToFloater;

function positionZoomToPanel() {
  var zoom_to_panel=document.getElementById("{$this->id}_panel");
  var zoom_to_tool=document.getElementById("tool_{$this->id}");
  zoomToLeft=findPosX(zoom_to_tool)+6;
  if (zoom_to_tool.offsetWidth) zoomToLeft+=zoom_to_tool.offsetWidth;
  zoomToTop=findPosY(zoom_to_tool);
  zoomToFloater.move(zoomToLeft,zoomToTop,0,0);
}
function showZoomToPanel(evt) {
  if (zoomToLeft==INVALID && zoomToTop==INVALID) positionZoomToPanel();
  zoomToFloater.show();
}
function moveZoomToPanel(evt) {
  var evt = evt || window.event;
  var dx=zoomToFloater.left-evt.clientX;
  var dy=zoomToFloater.top-evt.clientY;
  zoomToFloater.setOffsets(dx,dy);
  zoomToFloater.startTracking(evt);
}
function ztval(input) {
  var disableOthers; //boolean value defining whether to enable or
                     //disable a form field
  var type = "";

  //Get form object, if they exist
  var lat_north = document.getElementById("lat_north");
  var lat_south = document.getElementById("lat_south");
  var lon_west = document.getElementById("lon_west");
  var lon_east = document.getElementById("lon_east");
  var zoom_to_scale = document.getElementById("zoom_to_scale");
  var zoom_to_address = document.getElementById("zoom_to_address");

  if (input.id=="zoom_to_scale") {

      //If it exists, check if it has a value
      var is_zoom_to_scale = false;
      if (zoom_to_scale)
          is_zoom_to_scale = zoom_to_scale.value.length>0;

      //If there was a value, set to disable all other form values
      disableOthers = is_zoom_to_scale;
      type = "scale";
  } 
  else if (input.id == "lat_north" || input.id == "lat_south" ||
           input.id == "lon_west" || input.id == "lon_east") {

      //Check that they have a value
      var is_lat_north = is_lat_south = is_lon_west = is_lon_east = false;
      if (lat_north) {
          is_lat_north = lat_north.value.length>0;
      }
      if (lat_south) {
          is_lat_south = lat_south.value.length>0;
      }
      if (lon_west) {
          is_lon_west = lon_west.value.length>0;
      }
      if (lon_east) {
          is_lon_east = lon_east.value.length>0;
      }
      
      //if they have a value then disable all other form fields
      disableOthers=(is_lat_north ||
                     is_lat_south ||
                     is_lon_west ||
                     is_lon_east);
      type = "coords";
  } 
  else if (input.id == "zoom_to_address") {
      
      //Check that it has a value
      var is_zoom_to_address = false;
      if (zoom_to_address)
          is_zoom_to_address = zoom_to_address.value.length > 0;

      //If it has a value, set to disable all other form values
      disableOthers = is_zoom_to_address;
      type = "address";
  }

  //Disable all other groups of form fields
  if (type != "scale") {
      if (zoom_to_scale) {
          zoom_to_scale.disabled=disableOthers;
      }
  }
  if (type != "coords") {
      if (lat_north)
          lat_north.disabled=disableOthers;
      if (lat_south)
          lat_south.disabled=disableOthers;
      if (lon_west)
          lon_west.disabled=disableOthers;
      if (lon_east)
          lon_east.disabled=disableOthers;
  }
  if (type != "address") {
      if (zoom_to_address)
          zoom_to_address.disabled=disableOthers;
  }
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    $setup="";
    $onload=<<<EOT
  var toolObj=new Tool('{$this->id}','default',false,false,'{$this->selectionHandler}','{$this->lostFocusHandler}');
  toolObj.tooltip="{$this->tooltip}";
  panels.addTool(toolObj);
  zoomToFloater=new Floater("{$this->id}_panel");
  floaters.addFloater(zoomToFloater);
  zoomToFloater.addTrackingEvent("zoomToAnchor","onmousemove");
  zoomToFloater.addStopEvent("zoomToAnchor","onmouseup");\n
EOT;
    if ($this->isDefault) {
      $setup=<<<EOT
  panels.defaultTool="{$this->id}";
  {$this->clickHandler}();
  updateTooltip('{$this->id}');\n
EOT;
    }
    return $onload.$setup;
  }

  public function asHTML() {
    $class=($this->isDefault)?"tool_on":"tool_off";
    $mouse="onmouseover=\"updateTooltip('{$this->id}');\"";
    $html=<<<EOT
      <tr><td class="$class" id="tool_{$this->id}" onclick="{$this->clickHandler}();" $mouse>
        <img src="{$this->image}" alt="{$this->name}" title="{$this->description}" onclick="{$this->clickHandler}();" />
        <h4>{$this->name}</h4>
      </td></tr>\n
EOT;
    return $html;
  }
  
  public function additionalHTML() {
    global $image_path;
    // onmousedown="moveZoomToPanel(event);"
    $style="position:absolute; display:none; left:0px; top:0px; width:400px; height:auto; z-index:0;";
    $anchor_style="height:30px;";
    
    $html = <<<EOT
<div id="{$this->id}_panel" class="floating_panel" style="$style">
  <table cellspacing="0">
    <tr>
      <th width="18"><img src="{$image_path}/close.png" onclick="zoomToFloater.hide();" width="16" height="16" alt="Close" title="Close" /></th>
      <th id="zoomToAnchor" style="$anchor_style" onmousedown="moveZoomToPanel(event);" width="382">Zoom To</th>
    </tr>
EOT;

    if ($this->showZoomToScale) {
        $html .= <<<EOT
    <tr>
      <td colspan="2">Zoom to scale: (e.g. 1:24000)</td>
    </tr>
    <tr>
      <td colspan="2" align="center">1:<input type="text" id="zoom_to_scale" size="12" onchange="ztval(this);" /><hr></td>
    </tr>
EOT;
    }

    if ($this->showZoomToCoordinates) {
        $html .= <<<EOT
    <tr>
      <td colspan="2">Zoom to coordinates:</td>
    </tr>
    <tr>
      <td colspan="2">
      <table width="100%">
        <tr><td colspan="2" align="center">Lat. N <input type="text" id="lat_north" size="8" onchange="ztval(this);" /></td></tr>
        <tr>
          <td align="left">Lon. W <input type="text" id="lon_west" size="8" onchange="ztval(this);" /></td>
          <td align="right">Lon. E <input type="text" id="lon_east" size="8" onchange="ztval(this);" /></td>
        </tr>
        <tr><td colspan="2" align="center">Lat. S <input type="text" id="lat_south" size="8" onchange="ztval(this);" /><hr/></td></tr>
      </table>
      </td>
    </tr>
EOT;
    }

    if ($this->showZoomToAddress) {
        $html .= <<<EOT
    <tr>
      <td colspan="2">Zoom to Address:<br/></td>
    </tr>
    <tr>
      <td colspan="2">
      <table width="100%">
        <tr>
          <td>Address:</td>
          <td><input type="text" id="zoom_to_address" size="50" onchange="ztval(this);"></td>
        </tr>
      </table>
      </td>
    </tr>
EOT;
    }

    $html .= <<<EOT
    <tr>
      <td align="center" colspan="2">
        <input class="button" type="button" id="zoom_panel_button" value="Zoom" onclick="{$this->selectionHandler}();" />
      </td>
    </tr>
  </table>
</div>\n
EOT;

    return $html;
  }
}

?>
