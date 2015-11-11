 /***********************************************************************
 *
 * @file          mapstate.js
 *
 * $Id: mapstate.js 84 2006-12-15 17:06:03Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/mapstate.js $
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
 * This file provides a MapState object used to keep track of a maps state.
 *  - extent (extent_x1, extent_y1, ...)
 *  - dimensions (width, height)
 ***************************************************************************/

function MapState() {
  this.left=this.top=0;
  this.width=this.height=INVALID;
  this.isInitialized=false;
  return this;
}

/***************************************************************************
 * init()
 * initializes the state of a map. 
 ***************************************************************************/
MapState.prototype.init=function() {
  this.setCoordinates();

	this.extent_x1=this.extent_y1=this.extent_x2=this.extent_y2=0;
	this.latitude_north=this.latitude_south=this.longitude_east=this.longitude_west=0;
	this.latitude_mid=this.longitude_mid=0;
	this.dms=false;
	this.scale=this.image_width=this.image_height=0;
  this.projection=this.ellipsoid=this.datum=this.units="";
  this.isInitialized=false;
  this.assignedInfoTabIds=new Array();
  this.assignedInfoTabNames=new Array();
}

/***************************************************************************
 * setCoordinates()
 * parses and sets the map's left, top, width and height. 
 ***************************************************************************/
MapState.prototype.setCoordinates=function() {
  this.left=parseInt(mapdiv.style.left);
  this.top=parseInt(mapdiv.style.top);
  this.width=parseInt(mapdiv.style.width);
  this.height=parseInt(mapdiv.style.height);
  this.mapX1=this.left;
	this.mapY1=this.top;
	this.mapX2=this.left+this.width;
	this.mapY2=this.top+this.height;
}

/***************************************************************************
 * drawMapState()
 * creates a map info tab for displaying data related to the map. 
 ***************************************************************************/
MapState.prototype.drawMapState=function() {
  var state_panel=null;
  var state_id=this.getInfoTabId("Map Info");
  if (!state_id) {
    state_id=infoTabs.addTab("Map Info",100,270,180);
    this.assignedInfoTabNames.push("Map Info");
    this.assignedInfoTabIds.push(state_id);
  }
  if (state_id) {
    state_panel=document.getElementById(state_id);
    if (state_panel) {
      state_panel.innerHTML="Longitude West: "+this.longitude_west+"<br />"+
        "Longitude East: "+this.longitude_east+"<br />"+
        "Latitude North: "+this.latitude_north+"<br />"+
        "Latitude South: "+this.latitude_south+"<br />"+
        "Scale: 1:"+format_number(this.scale)+"<br />"+
        "Units: "+this.units+"<br />";
      if (this.projection.length>0) {
        state_panel.innerHTML+="Projection: "+this.projection+"<br />"+
          "Datum: "+this.datum+"<br />"+
          "Ellipsoid: "+this.ellipsoid;
      }
    }
  }
}

/***************************************************************************
 * setState()
 * parses the map state info returned from the creation/modification of a
 * map. 
 ***************************************************************************/
MapState.prototype.setState=function(contents) {
  // contents returned are extents (minx, maxx, miny, maxy), width, 
  // height, layers, scale, projection, lat/long, etc.
  if (contents.minx) {
    currentMapState.extent_x1=contents.minx;
    if (map.minx==0) map.minx=contents.minx;
  }
  if (contents.miny) {
    currentMapState.extent_y1=contents.miny;
    if (map.miny==0) map.miny=contents.miny;
  }
  if (contents.maxx) {
    currentMapState.extent_x2=contents.maxx;
    if (map.maxx==0) map.maxx=contents.maxx;
  }
  if (contents.maxy) {
    currentMapState.extent_y2=contents.maxy;
    if (map.maxy==0) map.maxy=contents.maxy;
  }
  if (!currentLayerState.isInitialized) {
    // send these off to the layer state object
    var groups=(contents.groups)?contents.groups:[];
    var layers=(contents.layers)?contents.layers:[];
    var layer_status=(contents.layer_status)?contents.layer_status:[];
    if (groups.length==layers.length && layers.length==layer_status.length) {
      var on,visible;
      for (var i=0;i<layers.length;++i) {
        on=(layer_status[i]=="on");
        visible=(groups[i].length>0);
        currentLayerState.addLayer(layers[i],groups[i],on,visible,visible);
      }
      currentLayerState.isInitialized=true;
    }
    currentLayerState.drawLayers();
  }
  if (contents.image_width) currentMapState.image_width=contents.image_width;
  if (contents.image_height) currentMapState.image_height=contents.image_height;
  if (contents.scale) {
    currentMapState.scale=contents.scale;
    if (map.maxScale==0) map.maxScale=contents.scale;
  }
  if (contents.units) currentMapState.units=contents.units;
  if (contents.projection) currentMapState.projection=contents.projection;
  if (contents.ellipsoid) currentMapState.ellipsoid=contents.ellipsoid;
  if (contents.datum) currentMapState.datum=contents.datum;
  if (contents.latitude_south) {
    currentMapState.latitude_south=contents.latitude_south;
    if (map.minLat==0) map.minLat=contents.latitude_south;
  }
  if (contents.latitude_north) {
    currentMapState.latitude_north=contents.latitude_north;
    if (map.maxLat==0) map.maxLat=contents.latitude_north;
  }
  if (contents.longitude_west) {
    currentMapState.longitude_west=contents.longitude_west;
    if (map.minLon==0) map.minLon=contents.longitude_west;
  }
  if (contents.longitude_east) {
    currentMapState.longitude_east=contents.longitude_east;
    if (map.maxLon==0) map.maxLon=contents.longitude_east;
  }
  if (contents.latitude_mid) currentMapState.latitude_mid=contents.latitude_mid;
  if (contents.longitude_mid) currentMapState.longitude_mid=contents.longitude_mid;
  this.isInitialized=true;
}

/***************************************************************************
 * getInfoTabId()
 * returns the element id of a named map info tab that belongs to the MapState
 * object. 
 ***************************************************************************/
MapState.prototype.getInfoTabId=function(tabName) {
  var tabId=null;
  for (var i=0;i<this.assignedInfoTabNames.length;++i) {
    if (this.assignedInfoTabNames[i]==tabName) {
      tabId=this.assignedInfoTabIds[i];
      break;
    }
  }
  return tabId;
}

/***************************************************************************
 * queryString()
 * compiles the querystring related to a map's state that is used in creating
 * or modifying a map image. 
 ***************************************************************************/
MapState.prototype.queryString=function() {
  var qs=new Array;
	qs.push("image_width="+this.width);
	qs.push("image_height="+this.height);
  // if our extent values are non-default / non-zero, add it to the query string
  if (this.extent_x1!=0 && this.extent_y1!=0 && this.extent_x2!=0 && this.extent_y2!=0) {
    qs.push("extent[x1]="+this.extent_x1);
    qs.push("extent[y1]="+this.extent_y1);
    qs.push("extent[x2]="+this.extent_x2);
    qs.push("extent[y2]="+this.extent_y2);
  }
  return qs.join("&");
}

MapState.prototype.getUnits = function() {
  return this.units;
}