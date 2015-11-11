 /***********************************************************************
 * @file          main.js
 *
 * $Id: main.js 109 2007-02-25 02:12:26Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/main.js $
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
 * This file contains global initialization and utility routines. 
 ***************************************************************************/

if (document.all && !document.getElementById) {
  document.getElementById = function(id) {
    return document.all[id];
  }
}

if (window.ActiveXObject)
  var isIE=true;
else
  var isIE=false;

// global constants
var INVALID=-1;

// global variables
var queue,map,panels,rubber,ref_rubber,currentMapState,currentLayerState,infoTabs, geocoderClient;
var mapdiv,map_image,glassdiv,alertdiv, loadimgdiv, bannerdiv,updatediv,panelsdiv,panelcommondiv;
var toolspaneldiv,groupspaneldiv,sourcepaneldiv,tooltipsdiv;
var floaters;
var coordsFloater;
var current_lat,current_lon;

var timerID;
function resizeToWindow() {
  window.clearTimeout(timerID);
  timerID=window.setTimeout('resize()',500);
}

// debugging
var debug=null;

// reusable width and height
var windowWidth=0,windowHeight=0;
var bannerHeight=0,panelsTop=0;

/***********************************************************************
* main_init()
* instantiates all global objects and does initial element sizing. it is
* called once -- in the application's init function that is called from
* the body onload event.
***********************************************************************/
function main_init() {
  mapdiv=document.getElementById("map");
  map_image=document.getElementById("map_image");
  glassdiv=document.getElementById("glass");
  alertdiv=document.getElementById("alerts");
  loadimgdiv=document.getElementById("loading_img");
  bannerdiv=document.getElementById("banner");
  updatediv=document.getElementById("update_div");
  panelsdiv=document.getElementById("panels");
  toolspaneldiv=document.getElementById("tools_panel");
  groupspaneldiv=document.getElementById("groups_panel");
  sourcepaneldiv=document.getElementById("source_panel");
  tooltipsdiv=document.getElementById("tooltips");
  panelcommondiv=document.getElementById("panels_common");
  current_lat=document.getElementById("current_lat");
  current_lon=document.getElementById("current_lon");
  
  glassdiv.style.zIndex=parseInt(mapdiv.style.zIndex)+1;

  //update windowWidth/windowHeight
  updateWindowParams();
  
  // get header height
  if (bannerdiv) bannerHeight=getHeight(bannerdiv);
  
  floaters=new RegisteredFloaters();
  
	// key objects
	queue=new Queue();
	map=new Map();
  panels=new Panels();
  rubber=new RubberBand();
  currentMapState=new MapState();
	currentLayerState=new LayerState();
	infoTabs=new TabGroup(false);

  if (window.GeocoderClient)
    geocoderClient = new GeocoderClient();

  if (debug_on)
    debug=new Debug();
  if (error_on)
    error=new Error();

  if (resize_map_to_window) {
    window.onresize=resizeToWindow;
    resize();
  }

  objectInit();

  // map coordinates display
  coordsFloater=new Floater("coords_panel");
  floaters.addFloater(coordsFloater);
  coordsFloater.setOffsets(10,10);

  if (floating_coords_toggle) {
    setFloatingCoordinatesEvents();
//    registerEvent(glassdiv,'onmouseover',showCoordinates);
//    registerEvent(glassdiv,'onmouseout',hideCoordinates);
  }

  if (fixed_coords) {
    registerEvent(glassdiv,'onmousemove',displayCoordinates);
  }
}

/***********************************************************************
* objectInit()
* initializes the global objects. this routine is called from main_init 
* when the page initially loads and when the state of these objects is
* invalidated (like when the map file changes).
***********************************************************************/
function objectInit() {
  // initialize the objects
  queue.init();
  map.init();
  panels.init();
  rubber.init();
  currentMapState.init();
  currentLayerState.init();

  // debugging output
  if (debug_on) 
    debug.init();

  if (error_on) 
    error.init();

}

/***********************************************************************
* toggleFloatingCoordinates(), setFloatingCoordinatesEvents(), 
* showCoordinates(), displayCoordinates(), hideCoordinates()
* 
* the following 5 routines manage the coordinates display. they display
* the mouse's position on the map as lat/long coordinates either as a
* floating or fixed display that can toggle back and forth.
***********************************************************************/
function toggleFloatingCoordinates() {
    floating_coords=document.getElementById("floating_coords").checked;
    setFloatingCoordinatesEvents();
    if (!floating_coords) coordsFloater.hide();
    resizePanels();
}
function setFloatingCoordinatesEvents() {
//  floating_coords=document.getElementById("floating_coords").checked;
  if (floating_coords) {
    registerEvent(glassdiv,'onmousemove',displayCoordinates);
    registerEvent(glassdiv,'onmouseover',showCoordinates);
    registerEvent(glassdiv,'onmouseout',hideCoordinates);
  } else {
    unregisterEvent(glassdiv,'onmousemove',displayCoordinates);
    unregisterEvent(glassdiv,'onmouseover',showCoordinates);
    unregisterEvent(glassdiv,'onmouseout',hideCoordinates);
  }
}
function showCoordinates(evt) {
  if (coordsFloater.isVisible) return;
  coordsFloater.startTracking(evt);
  coordsFloater.show();
}
function displayCoordinates(evt) {
  if (!currentMapState.isInitialized) return;
  var evt = evt || window.event;

//Convert cursor location (x/y pixels) to lat/lng decimal degrees

  //percentage of x and y that pointer is at on the map image from the bottom left 
  //of the map image relative to total height and width
  var w_perc = (evt.clientX-currentMapState.left)/currentMapState.width;
  var h_perc = (currentMapState.height-(evt.clientY-currentMapState.top))/currentMapState.height;

  //Extract extent as integer (whole portion)
  var east = currentMapState.longitude_east;
  var west = currentMapState.longitude_west;
  var north = currentMapState.latitude_north;
  var south = currentMapState.latitude_south;

  //Calculate difference (integer)
  var diff_lon=Math.abs(east-west);
  var diff_lat=Math.abs(north-south);

//whole + fraction
var lon = (diff_lon * w_perc) + west;
var lat = (diff_lat * h_perc) + south;

  //Round to 4 decimal places
  var dec=10000;
  lon = Math.round(lon * dec) / dec;
  lat = Math.round(lat * dec) / dec;

  if (floating_coords) {
    var coords="Latitude = "+lat+"<br />Longitude = "+lon;
    coordsFloater.me.innerHTML=coords;
    coordsFloater.moveTracking(evt);
  }
  
  if (fixed_coords) {
    current_lat.innerHTML=lat;
    current_lon.innerHTML=lon;
  }
}
function hideCoordinates(evt) {
  var evt = evt || window.event;
  if (evt.clientX>=currentMapState.left && evt.clientY>=currentMapState.top) return;
  coordsFloater.stopTracking(evt);
  coordsFloater.hide();
}

function registerEvent(element,eventType,handler) {
  eventType=determineEventType(element,eventType);
  if (element.attachEvent)
    element.attachEvent(eventType,handler);
  else if (element.addEventListener)
    element.addEventListener(eventType,handler,false);
}

function unregisterEvent(element,eventType,handler) {
  eventType=determineEventType(element,eventType);
  if (element.detachEvent)
    element.detachEvent(eventType,handler);
  else if (element.removeEventListener)
    element.removeEventListener(eventType,handler,false);
}

function determineEventType(element,eventType) {
  if (element.attachEvent) {
    // attachEvent expects the event type with an "on" prefix, 
    // like "onclick" and not simply "click"
    if (eventType.length>2 && eventType.substr(0,2)!="on")
      eventType="on"+eventType;
  } else if (element.addEventListener) {
    // addEventListener expects the event type without an
    // "on" prefix, like simply "click" and not "onclick"
    if (eventType.length>2 && eventType.substr(0,2)=="on")
      eventType=eventType.substr(2);
  }
  return eventType;
}

/***********************************************************************
* resize()
* resizes the interface components. it expects either:
* 1) the global variable resize_map_to_window is true, OR
* 2) 2 optional arguments for map width and map height (in that order) 
***********************************************************************/
function resize() {
  if (!resize_map_to_window && arguments.length!=2) {
    error.print("Unable to resize the interface: no map width or height have been provided");
    return;
  }

  var mapWidth=null,mapHeight=null;
  var top=0,left=0,bannerHgt=bannerHeight+1;
  
  top=bannerHgt;

  // resize the layers, tools and datasource panels
  panelsdiv.style.top=top+"px";
  top+=getHeight(panelsdiv)+1;
  panelsTop=top;
  left=getWidth(panelsdiv);
  resizePanels();
  
  // resize the map
  mapdiv.style.top=glassdiv.style.top=bannerHgt+"px";
  glassdiv.style.left=mapdiv.style.left;
  
  if (arguments.length==2) {
    mapWidth=arguments[0];
    mapHeight=arguments[1];
  } else if (resize_map_to_window) {    
    //update windowWidth/windowHeight
    updateWindowParams();
    mapWidth=windowWidth-left-2;
    mapHeight=windowHeight-bannerHgt-2;
  }
  
  if (mapWidth && mapHeight) {
    mapdiv.style.width=glassdiv.style.width=mapWidth+"px";
    mapdiv.style.height=glassdiv.style.height=mapHeight+"px";
    map_image.width=mapWidth;
    map_image.height=mapHeight;
    if (currentMapState) currentMapState.setCoordinates();
    if (map && map.isDrawn) {
      //Hide the map image, it will be re-show via map_image onload
      map_image.style.display="none";
      map.createMap();
    }
  }
}

function updateWindowParams() {
    // get window width and height
    if (self.innerWidth) {
      windowWidth=self.innerWidth-1;
      windowHeight=self.innerHeight-1;
    } else if (document.documentElement && document.documentElement.clientWidth) {
      windowWidth=document.documentElement.clientWidth-1;
      windowHeight=document.documentElement.clientHeight-1;
    } else if (document.body.clientWidth) {
      windowWidth=document.body.clientWidth-1;
      windowHeight=document.body.clientHeight-1;
    } else {
      windowWidth=document.body.offsetWidth-1;
      windowHeight=document.body.offsetHeight-1;
    }
}

function moveElement(el,left,top) {
//  if (el.offsetX!=0) left+=el.offsetX;
//  if (el.offsetY!=0) top+=el.offsetY;

  if (el.me) {
    el.me.style.left=left+"px";
    el.me.style.top=top+"px";
  }

  if (el.style) {
    el.style.left=left+"px";
    el.style.top=top+"px";;
  }
  
  el.left=left;
  el.top=top;
}

/***********************************************************************
* resizePanels()
* resizes the layers, tools and datasource panels.
***********************************************************************/
function resizePanels() {
  var top=panelsTop;
  var commonHgt=getHeight(panelcommondiv);
  var panelHgt=windowHeight-commonHgt-top-2;
  toolspaneldiv.style.top=top+"px";
  toolspaneldiv.style.height=panelHgt+"px";
  groupspaneldiv.style.top=top+"px";
  groupspaneldiv.style.height=panelHgt+"px";
  sourcepaneldiv.style.top=top+"px";
  sourcepaneldiv.style.height=panelHgt+"px";
  top+=panelHgt;
  panelcommondiv.style.top=top+"px";
}

function redrawTools(status,response) {
  if (status=="OK") {
    
  } else {
    error.print("Tool re-initialization:\n"+status+"\n"+response);
  }
}

/***********************************************************************
* showAlert(), hideAlert()
* shows/hides an alert message on top of the map.  Useful for user/system
* errors and notifications
***********************************************************************/
function showAlert(message) {
  var alertHeight = 120;
  var alertWidth = 300;
  alertdiv.innerHTML = message;
  alertdiv.style.top=(currentMapState.top + currentMapState.height/2 - alertHeight/2) + "px";
  alertdiv.style.left=(currentMapState.left + currentMapState.width/2 - alertWidth/2) + "px";
  alertdiv.style.display="block";
  alertdiv.style.zIndex=500;
}

function hideAlert() {
  alertdiv.style.display="none";
}

function showLoading(message) {
  loadimgdiv.style.top=(currentMapState.top + currentMapState.height/2) - loadimgdiv.height/2 + "px";
  loadimgdiv.style.left=(currentMapState.left + currentMapState.width/2) - loadimgdiv.width/2 + "px";
  loadimgdiv.style.zIndex=500;
  loadimgdiv.style.display="block";
}

function hideLoading() {
  loadimgdiv.style.display="none";
}

/*****************************************************************************
* format_number()
* formats a numeric string with commas for easier user viewing. it was
* shamelessly borrowed from the good folks at
* http://www.mredkj.com/javascript/numberFormat.html.
*****************************************************************************/
function format_number(num) {
  num += '';  // cast it to a string
  var x = num.split('.');
  var x1 = x[0];
  var x2 = (x.length > 1) ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

/*****************************************************************************
 * newline_to_break(str)
 * Replaces newlines in a string with html line breaks
 *
*****************************************************************************/
function newline_to_break(str) {
  return str.replace(/\n/g, "<br/>");
}

/*****************************************************************************
* findPosX(), findPosY()
* each returns a given element object's left and top positions. they were
* shamelessly borrowed from the good folks at
* http://www.quirksmode.org/js/findpos.html.
*****************************************************************************/
function findPosX(obj) {
  var curleft = 0;
  if (obj.offsetParent) {
    while (obj.offsetParent) {
      curleft += obj.offsetLeft
      obj = obj.offsetParent;
    }
  } else if (obj.x) {
    curleft += obj.x;
  }
  return curleft;
}
function findPosY(obj) {
  var curtop = 0;
  if (obj.offsetParent) {
    while (obj.offsetParent) {
      curtop += obj.offsetTop
      obj = obj.offsetParent;
    }
  } else if (obj.y) {
    curtop += obj.y;
  }
  return curtop;
}

/*****************************************************************************
* getWidth(), getHeight()
* each returns a given element object's width and height, depending on how
* the browser and how that info is stored.
*****************************************************************************/
function getWidth(obj) {
  if (obj.innerWidth)
    return obj.innerWidth;
  else if (obj.style && obj.style.width)
    return parseInt(obj.style.width);
  else if (obj.clientWidth)
    return obj.clientWidth;
  else
    return obj.offsetWidth;
}
function getHeight(obj) {
  if (obj.innerHeight)
    return obj.innerHeight;
  else if (obj.style && obj.style.height)
    return parseInt(obj.style.height);
  else if (obj.clientHeight)
    return obj.clientHeight;
  else
    return obj.offsetHeight;
}
function setWidth(obj, width) {
  if (obj.innerWidth)
    obj.innerWidth = width;
  else if (obj.style && obj.style.width)
    obj.style.width = width;
  else if (obj.clientWidth)
    obj.clientWidth = width;
  else
    obj.offsetWidth = width;
}
function setHeight(obj, height) {
  if (obj.innerHeight) {
    obj.innerHeight = height;
  } else if (obj.style && obj.style.height) {
    obj.style.height = height;
  } else if (obj.clientHeight) {
    obj.clientHeight = height;
  } else {
    obj.offsetHeight = height;
  }
}

/*****************************************************************************
* openWin()
* opens a new window.
*****************************************************************************/
function openWin(page,options) {
  window.open(page,'',options)
}
