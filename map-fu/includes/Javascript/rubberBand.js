 /***********************************************************************
 *
 * @file          rubberBand.js
 *
 * $Id: rubberBand.js 104 2007-01-11 05:42:43Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/rubberBand.js $
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
 * This file contains the RubberBand object, used in recording coordinates of
 * mouse events (such as click and move) and calculating those coordinates into
 * a meaningful set of pixel coordinates on the map image that can be used by
 * mapserver in zooming and panning activities.
 ***************************************************************************/

function RubberBand() {
  this.div=document.getElementById('rubberBand');
  return this;
}

/****************************************************************************
 * init()
 * initializes the RubberBand object's state.
 ***************************************************************************/ 
RubberBand.prototype.init=function() {
  this.isPoint=false;
  this.isRectangle=false;
  this.start_x = this.start_y = 0;
  this.mouse_x = this.mouse_y = 0;
  this.x1=this.y1=this.x2=this.y2=0;
  this.offsetX=this.offsetY=0;
}

/****************************************************************************
 * pointRubber()
 * records the position of a click event, removes further event handling and
 * calculates the coordinates on the map image (in pixels) of the point
 * clicked. 
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RubberBand object, but rather to the calling object. so any reference to the
 * RubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RubberBand
 * object).
 ***************************************************************************/ 
RubberBand.prototype.pointRubber=function(evt) {
  // set up our constant start_x and start_y
  var evt = evt || window.event;
  rubber.start_x=evt.clientX;
  rubber.start_y=evt.clientY;
  rubber.mouse_x=evt.clientX;
  rubber.mouse_y=evt.clientY;
  
  unregisterEvent(document,'onmousemove',rubber.moveRubber);
  unregisterEvent(document,'onmousedown',rubber.moveRubber);
  unregisterEvent(document,'onmouseup',rubber.stopRubber);
  
  rubber.calculateCoordinates();
  if (panels.currentTool.selectionHandler && panels.currentTool.selectionHandler.length>0) {
    eval(panels.currentTool.selectionHandler+"();");
  }
}

/****************************************************************************
 * startRubber()
 * records the starting position of the click event that indicates where the
 * rubberband should be drawn from. also sets the events that will track the
 * rubberband and stop it's recording. 
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RubberBand object, but rather to the calling object. so any reference to the
 * RubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RubberBand
 * object).
 ***************************************************************************/ 
RubberBand.prototype.startRubber=function(evt) {
  // set up our constant start_x and start_y
  var evt = evt || window.event;
  rubber.start_x=evt.clientX;
  rubber.start_y=evt.clientY;
  
  // set up the div and make it visible
  rubber.div.style.left=rubber.start_x;
  rubber.div.style.top=rubber.start_y;
  rubber.div.style.width=0;
  rubber.div.style.height=0;
  if (panels.currentTool.showRubberBand)
    rubber.div.style.display="block";
  else
    rubber.div.style.display="none";
  
  // map our event handlers
  registerEvent(document,'onmousemove',rubber.moveRubber);
  registerEvent(document,'onmousedown',rubber.moveRubber);
  registerEvent(document,'onmouseup',rubber.stopRubber);
}
  
/****************************************************************************
 * moveRubber()
 * calculates the current position of the rubberband object and keeps it from
 * going out of the bounds of the map image.
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RubberBand object, but rather to the calling object. so any reference to the
 * RubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RubberBand
 * object).
 ***************************************************************************/ 
RubberBand.prototype.moveRubber=function(evt) {
  // current mouse position
  var evt = evt || window.event;
  rubber.mouse_x=evt.clientX;
  rubber.mouse_y=evt.clientY;

  // check that we're within map box bounds and if not, update mouse position
  if(rubber.mouse_x<=currentMapState.mapX1) rubber.mouse_x=currentMapState.mapX1+2;
  if(rubber.mouse_y<=currentMapState.mapY1) rubber.mouse_y=currentMapState.mapY1+2;
  if(rubber.mouse_x>=currentMapState.mapX2) rubber.mouse_x=currentMapState.mapX2-2;
  if(rubber.mouse_y>=currentMapState.mapY2) rubber.mouse_y=currentMapState.mapY2-2;
  
  // diff_x, diff_y: difference between beginning click position and
  // current mouse position
  var diff_x=rubber.mouse_x-rubber.start_x;
  var diff_y=rubber.mouse_y-rubber.start_y;
  var r=rubber.div;
  
  var width,height;
  var left=rubber.start_x;
  var width=Math.abs(diff_x);
  if (diff_x>0)
    width-=1;
  else if (diff_x<0)
    left+=diff_x+1; 
  
  var top=rubber.start_y;
  var height=Math.abs(diff_y);
  if (diff_y>0)
    height-=1;
  else if (diff_y<0)
    top+=diff_y;
  
  r.style.top=top+"px";
  r.style.left=left+"px";
  r.style.width=width+"px";
  r.style.height=height+"px";

	if (panels.currentTool.mapBackgroundMoves) {

    if (!rubber.map_start_init) {
      rubber.map_start_init = true;
      rubber.map_start_x = findPosX(mapdiv);
      rubber.map_start_y = findPosY(mapdiv);

      //Hide the image
      map_image.style.display = "none";

      //Load the background image, we'll shift that within the div on
      //drag instead of the map div which has problems with floating
      //over the top of all the other containers.  z-index issues
      var bg="transparent url(\""+map_image.src+"\") no-repeat scroll center";
      mapdiv.style.background=bg;
    }

		mapdiv.style.backgroundPosition = (diff_x) + "px "+ (diff_y) +"px";
		mapdiv.style.backgroundRepeat = "no-repeat";
		mapdiv.style.cursor = "move";
	}
}

/****************************************************************************
 * stopRubber()
 * stops tracking the movement of the rubberband and records its ending
 * coordinates.
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RubberBand object, but rather to the calling object. so any reference to the
 * RubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RubberBand
 * object).
 ***************************************************************************/ 
RubberBand.prototype.stopRubber=function(evt) {
  var r = rubber.div;
  r.style.height = "0px";
  r.style.width = "0px";
  r.style.top = "0px";
  r.style.left = "0px";
  r.style.display = "none";

	if (panels.currentTool.mapBackgroundMoves) {

    //Clear the background image.  map_image will be re-shown via
    //onload event after it is completely transferred to the client
    mapdiv.style.background="";
    //Reset so that init is done again next time rubber band is clicked
    rubber.map_start_init = false;
  }
  
  // remove our event handlers
  unregisterEvent(document,'onmousemove',rubber.moveRubber);
  unregisterEvent(document,'onmousedown',rubber.moveRubber);
  unregisterEvent(document,'onmouseup',rubber.stopRubber);
  
  rubber.calculateCoordinates();
  if (panels.currentTool.selectionHandler && panels.currentTool.selectionHandler.length>0) {
    eval(panels.currentTool.selectionHandler+"();");
  }
}

/****************************************************************************
 * calculateCoordinates()
 * calculates the coordinates on the map image (in pixels) of the rubberband's
 * last bounding action. this may be a single point or 2 points that define
 * a bounding box (upper left and lower right). 
 ***************************************************************************/ 
RubberBand.prototype.calculateCoordinates=function() {
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
   ************************************************************************/
  this.isPoint=this.isRectangle=false;
  if (this.start_x>currentMapState.mapX1 && this.start_x<currentMapState.mapX2 &&
      this.start_y>currentMapState.mapY1 && this.start_y<currentMapState.mapY2 &&
      this.mouse_x>currentMapState.mapX1 && this.mouse_x<currentMapState.mapX2 &&
      this.mouse_y>currentMapState.mapY1 && this.mouse_y<currentMapState.mapY2) {
    // bounding coordinates are in range (inside the map image)
    // set x1 to the left most x value and x2 to the right most x value
    this.x1=this.start_x-currentMapState.mapX1;
    this.x2=this.mouse_x-currentMapState.mapX1;
    // set y1 to the top most y value and y2 to the bottom most y value
    this.y1=this.start_y-currentMapState.mapY1;
    this.y2=this.mouse_y-currentMapState.mapY1;

    // determine if the selection is a point or a rectangle
    var point=(Math.abs(this.x2-this.x1)<4 && Math.abs(this.y2-this.y1)<4);
    this.isPoint=point;
    this.isRectangle=!point;
  }
}

/****************************************************************************
 * queryString()
 * returns the coordinates of the last bounding action that the 
 * rubberband performed as a querystring of the form zoom['x1']=x1, ...
 * it is the reponsibility of the caller to specify a zoom factor if one
 * is required, or any other parameters that may be needed to complete the
 * operation (like zoom scale).
 ***************************************************************************/ 
RubberBand.prototype.queryString=function(queryType) {
  var qs=new Array();
  
  qs.push(queryType+"[x1]="+this.x1);
  qs.push(queryType+"[y1]="+this.y1);
  if (this.isRectangle) {
    qs.push(queryType+"[x2]="+this.x2);
    qs.push(queryType+"[y2]="+this.y2);
  }
  // additional arguments may be zoom factor, offset, zoom to scale, etc.
  for (var i=1;i<arguments.length;++i) {
    qs.push(arguments[i]);
  }
  
  return qs.join("&");
}
