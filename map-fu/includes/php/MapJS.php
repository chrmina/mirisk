<?php
 /***********************************************************************
 *
 * @file          MapJS.php
 *
 * $Id: MapJS.php 118 2007-04-18 03:55:24Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/MapJS.php $
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
 * This file contains the class MapJS. It is intended to provide the
 * ability to draw and manage a map client-side.  
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/

require_once($abstract_path."Component.php");

class MapJS extends Component {

  function __construct() {
  }

  function __destruct() {
  }

  public function asHTML() {
      return "";
  }

  public function inlineJavascript() {      
      global $after_map_load_js;
      global $php_path;

/****************************** Start Javascript ****************************/
      $js=<<<EOT

/****************************************************************************
 * The map object deals with drawing and managing the map.
 ***************************************************************************/

/*******************************************************************
* Map - the main map object
* this object handles all map related queries and functionality
*******************************************************************/ 
function Map() {
  this.isDrawn=false;
  this.queryString="";
  this.lastCursor="default";
  
  return this;
}

/***************************************************************************
 * init()
 * called when the map object needs to be initialized, such as the document
 * onload event or when the mapfile changes.  
 ***************************************************************************/
Map.prototype.init=function() {
  this.queryString="";
  this.isDrawn=false;
  this.minx=this.miny=this.maxx=this.maxy=0;
  this.minLat=this.minLon=this.maxLat=this.maxLon=0;

  this.map_image = "";
  this.refmap_image = "";
  this.scalebar_image = "";

  this.maxScale=0;
  this.minScale=24000;
  this.assignedInfoTabIds=new Array();
  this.assignedInfoTabNames=new Array();
  this.latestQueryDestination="";
  if (mapdiv.style && mapdiv.style.cursor!="default")
    this.lastCursor=mapdiv.style.cursor;
}
  
/***************************************************************************
 * updateMap()
 * tells the map object that the map needs to be redrawn with new data 
 * (new extents, new layers, new colors, etc). the redraw parameter is a
 * boolean that describes whether or not to redraw the map immediately. it
 * is the reponsibility of the caller to add any addition query string
 * parameters that may be needed.
 ***************************************************************************/
Map.prototype.updateMap=function(queryString,redraw) {
  this.setQueryString();
  if (queryString && queryString.length>0)
    this.queryString+="&"+queryString;
  if (redraw) this.draw();
}

/***************************************************************************
 * createMap()
 * blanks the map image (if one already exists) and calls draw to draw the
 * map image.
 ***************************************************************************/
Map.prototype.createMap=function() {
  // blank the map
  mapdiv.style.background="transparent url(\"\") no-repeat scroll center";
  map.isDrawn=false;
  
  this.setQueryString();
  this.draw();
}

/***************************************************************************
 * draw()
 * redraws the map image
 ***************************************************************************/
Map.prototype.draw=function() {
  if (mapdiv.style && mapdiv.style.cursor!="default")
    this.lastCursor=mapdiv.style.cursor;
  mapdiv.style.cursor="wait";
  showLoading("Loading");
  // draw the map
  var url="{$php_path}map.php?"+this.queryString;
  //queue.EnqueueItem("map creation",map.notifyMap,url,true);
  queue.EnqueueItem("map creation",map.notifyMap,url,false);
}

/***************************************************************************
 * getInfoTabId()
 * returns the element id of a named map info tab that belongs to the Map
 * object. 
 ***************************************************************************/
Map.prototype.getInfoTabId=function(tabName) {
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
 * setQueryString()
 * compiles the querystring used in creating the map image
 ***************************************************************************/
Map.prototype.setQueryString=function() {
	var qs=new Array();
	var temp;
	if (mapfile && mapfile.length>0)
	  qs.push("mapfile="+escape(mapfile));
	else
	  return;

  //Pass old image names to be removed from server
  if (this.map_image && this.map_image.length>0)
    qs.push("old_map_image="+escape(this.map_image));

  if (map.refmap_image && map.refmap_image.length>0)
    qs.push("old_refmap_image="+escape(map.refmap_image));

  if (map.scalebar_image && map.scalebar_image.length>0)
    qs.push("old_scale_image="+escape(map.scalebar_image));

	if (session_id && session_id.length>0)
	  qs.push("session_id="+escape(session_id));
	else
	  return;

	if (this.minx!=0 && this.miny!=0 && this.maxx!=0 && this.maxy!=0) {
	  qs.push("max_extent[x1]="+this.minx);
	  qs.push("max_extent[y1]="+this.miny);
	  qs.push("max_extent[x2]="+this.maxx);
	  qs.push("max_extent[y2]="+this.maxy);
	}

	temp=currentLayerState.queryString();
	if (temp.length>0) qs.push(temp);
	temp=currentMapState.queryString();
	if (temp.length>0) qs.push(temp);

	this.queryString=qs.join("&");
}

/***************************************************************************
 * notifyMap()
 * handles the notification of a map creation. this function is called from
 * the queue object when a map has finished being created. the response
 * parameter is an XML DOM object that contains the information related to
 * the new map, such as the path to the map image, extents, scale, etc.   
 ***************************************************************************/
Map.prototype.notifyMap=function(status,response) {
  if (status=="OK") {
    var contents=parseJSON(response);
    if (contents) {
      // the variables today and ms are used in a hack to force images to be
      // retrievedfrom the server (rather than stale images from the browser's

      // cache)
      var today=new Date();
      var ms=today.getMilliseconds();
      var url,bg;

      // map image
      if (contents.map_image) {
        map.map_image = contents.map_image;
        var map_image=document.getElementById("map_image");
        map_image.src=map.map_image+"?"+ms.valueOf();
        map.isDrawn=true;
      } else {
        error.print("map url NOT found");
      }

      // reference map
      if (contents.refmap_image) {
        map.refmap_image = contents.refmap_image;
        url=map.refmap_image+"?"+ms.valueOf();
        map.drawReferenceMap(url);
      }

      // map state
      currentMapState.setState(contents);
      currentMapState.drawMapState();
      currentMapState.isInitialized=true;

      // scalebar
      if (contents.scalebar_image) {
        map.scalebar_image = contents.scalebar_image;
        url=map.scalebar_image+"?"+ms.valueOf();
        map.drawScalebar(url);
      }

      // Notify all other components
      $after_map_load_js

      //Show the map container, it may have been hidden during window resize/map refresh
      mapdiv.style.display="block";

    } else {
      error.print("Map Image Creation:<br/>"+response);
      hideLoading();
    }
    if (updatediv) {
      updatediv.style.display="block";
    }
  } else {
    error.print("Map Image Creation:<br/>"+status+"<br/>"+response);
    hideLoading();
  }
  mapdiv.style.cursor=map.lastCursor;
}

/***************************************************************************
 * notifyQuery()
 * handles the notification of a map query. this function is called from
 * the queue object when a query has finished being processed. 
 ***************************************************************************/
Map.prototype.notifyQuery=function(status,response) {
  if (status=="OK") {
    document.getElementById(map.latestQueryDestination).innerHTML=response;
  } else {
    error.print("Query Area: "+status+", "+response);
  }
}

/***************************************************************************
 * queryArea()
 * fires off a request for a query procedure to the queue object.
 * 
 * queryProcessor: the server-side file that will do the query processing
 *   and formatting.
 * queryDestination: the document element id of the container that will hold
 *   the results of the query.
 * queryString: any additional parameters that need to be passed to the query
 *   processor. 
 ***************************************************************************/
Map.prototype.queryArea=function(queryProcessor,queryDestination,queryString) {
  this.latestQueryDestination=queryDestination;
  this.setQueryString();
  var qs=this.queryString;
  if (queryString && queryString.length>0)
    qs+="&"+queryString;
  var url=queryProcessor+"?"+qs;
  queue.EnqueueItem("map query",map.notifyQuery,url,false);
}

/***************************************************************************
 * drawReferenceMap()
 * creates a map info tab to hold the reference map for the currently displayed
 * map. 
 ***************************************************************************/
Map.prototype.drawReferenceMap=function(refmap_image) {
  var refmap_panel=null;
  var refmap_id=this.getInfoTabId("Refmap");
  if (!refmap_id) {
    refmap_id=infoTabs.addTab("Reference Map",130,205,140);
    this.assignedInfoTabNames.push("Refmap");
    this.assignedInfoTabIds.push(refmap_id);
/*
    //Create rubber band for reference map, attach to reference map panel
    ref_rubber = new RefRubberBand(refmap_id);
    ref_rubber.init();
    if (refmap_panel)
      registerEvent(refmap_panel,'onmousedown',ref_rubber.startRubber);
    else
      error.print("couldn't get ref map container to attach event");
*/
  }
  refmap_panel=document.getElementById(refmap_id);
  if (refmap_id) {
    if (refmap_panel) {
      var bg="#f0f0f0 url(\""+refmap_image+"\") no-repeat";
      try {
	      refmap_panel.style.background=bg;
	    } catch (e) {
	      error.print("Error creating reference map: "+e.message);
	    }
    }
  }
}

/***************************************************************************
 * drawScalebar()
 * creates a map info tab to hold the scalebar for the currently displayed
 * map. 
 ***************************************************************************/
Map.prototype.drawScalebar=function(scalebar_image) {
  var scalebar_panel=null;
  var scalebar_id=this.getInfoTabId("Scalebar");
  if (!scalebar_id) {
    scalebar_id=infoTabs.addTab("Scalebar",100,290,80);
    this.assignedInfoTabNames[this.assignedInfoTabNames.length]="Scalebar";
    this.assignedInfoTabIds[this.assignedInfoTabIds.length]=scalebar_id;
  }
  if (scalebar_id) {
    scalebar_panel=document.getElementById(scalebar_id);
    if (scalebar_panel) {
      var bg="#e1cbae url(\""+scalebar_image+"\") no-repeat scroll";
      try {
	      scalebar_panel.style.background=bg;
	    } catch (e) {
	      error.print("Error creating scalebar: "+e.message);
	    }
    }
  }
}

EOT;
/******************************* End Javascript *****************************/

    return $js;
  }
}
?>
