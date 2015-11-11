 /***********************************************************************
 *
 * @file          ref_rubber_band.js
 *
 * $Id: ref_rubber_band.js 102 2007-01-05 00:07:22Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/ref_rubber_band.js $
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
* This file contains the RefRubberBand object, used in recording 
* coordinates of mouse events (such as click and move) on a reference 
* map image and calculating those coordinates into a meaningful set of 
* pixel coordinates on the map image that can be used by mapserver in 
* zooming and panning activities.  
* div_id - the id of the container to use the rubber band
***************************************************************************/

function RefRubberBand(par_div_id) {
  this.div = document.getElementById("ref_rubberband");
  this.par_div = document.getElementById(par_div_id);
  return this;
}

/****************************************************************************
 * init()
 * initializes the RefRubberBand object's state.
 ***************************************************************************/ 
RefRubberBand.prototype.init=function() {
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
 * RefRubberBand object, but rather to the calling object. so any reference to the
 * RefRubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RefRubberBand
 * object).
 ***************************************************************************/ 
RefRubberBand.prototype.pointRubber=function(evt) {
  // set up our constant start_x and start_y
  var evt = evt || window.event;
  ref_rubber.start_x=evt.clientX;
  ref_rubber.start_y=evt.clientY;
  ref_rubber.mouse_x=evt.clientX;
  ref_rubber.mouse_y=evt.clientY;
  
  unregisterEvent(document,'onmousemove',ref_rubber.moveRubber);
  unregisterEvent(document,'onmousedown',ref_rubber.moveRubber);
  unregisterEvent(document,'onmouseup',ref_rubber.stopRubber);
  
  ref.rubber.calculateCoordinates();
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
 * RefRubberBand object, but rather to the calling object. so any reference to the
 * RefRubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RefRubberBand
 * object).
 ***************************************************************************/ 
RefRubberBand.prototype.startRubber=function(evt) {

  // set start_x and start_y to place where ref map onclick occurred
  var evt = evt || window.event;
  ref_rubber.start_x=evt.clientX;
  ref_rubber.start_y=evt.clientY;
  
  // set up the div and make it visible
  ref_rubber.div.style.left=ref_rubber.start_x;
  ref_rubber.div.style.top=ref_rubber.start_y;
  ref_rubber.div.style.width=10;
  ref_rubber.div.style.height=10;
//  if (panels.currentTool.showRubberBand)
    ref_rubber.div.style.display="block";
//  else
//    ref_rubber.div.style.display="none";
  
  // map our event handlers
  registerEvent(document,'onmousemove',ref_rubber.moveRubber);
  registerEvent(document,'onmousedown',ref_rubber.moveRubber);
  registerEvent(document,'onmouseup',ref_rubber.stopRubber);
}
  
/****************************************************************************
 * moveRubber()
 * calculates the current position of the rubberband object and keeps it from
 * going out of the bounds of the map image.
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RefRubberBand object, but rather to the calling object. so any reference to the
 * RefRubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RefRubberBand
 * object).
 ***************************************************************************/ 
RefRubberBand.prototype.moveRubber=function(evt) {
  //Get current mouse position
  var evt = evt || window.event;
  ref_rubber.mouse_x=evt.clientX;
  ref_rubber.mouse_y=evt.clientY;

  //Get the style of the ref rubber band
  rub_style = ref_rubber.div.style;

  var rub_left = parseInt(rub_style.left);
  var rub_top = parseInt(rub_style.top);
  var rub_height = parseInt(rub_style.height);
  var rub_width = parseInt(rub_style.width);

/*
  debug.print("top: " + rub_top);
  debug.print("left: " + rub_left);
  debug.print("height: " + rub_height);
  debug.print("width: " + rub_width);
*/

  //Get the style of the parent container of ref rubber band
  rub_par_style = ref_rubber.par_div.style;

  var max_left = parseInt(rub_par_style.left);
  var max_top = parseInt(rub_par_style.top);
  var max_height = parseInt(rub_par_style.height);
  var max_width = parseInt(rub_par_style.width);
  var max_right = max_left + max_width
  var max_bottom = max_top - max_height;

  // check that we're within the bounds of the reference map panel.  If we're suddenly not then
  // stop the rubber band at the border of the reference map panel.

  //If left of container
  if(ref_rubber.mouse_x < max_left) {
    ref_rubber.mouse_x = max_left;
  }

  //If right of container
  if (ref_rubber.mouse_x > max_right) {
    ref_rubber.mouse_x = max_right;
  }

//  debug.print("mouse_y: " + ref_rubber.mouse_y);
//  debug.print("max_top: " + max_top);

  //If above the container
  if(ref_rubber.mouse_y > max_top) {
    ref_rubber.mouse_y = max_top;
  }

  //If below the container
  if(ref_rubber.mouse_y < max_bottom) {
    ref_rubber.mouse_y = max_bottom;
  }
  
  // diff_x, diff_y: difference between beginning click position and
  // current mouse position
  var diff_x=ref_rubber.mouse_x-ref_rubber.start_x;
  var diff_y=ref_rubber.mouse_y-ref_rubber.start_y;

  var r=ref_rubber.div;  
  var width, height = 0;
  var left=ref_rubber.start_x;
  var width=Math.abs(diff_x);

  if (diff_x>0)
    width-=1;
  else if (diff_x<0)
    left+=diff_x+1; 
  
  var top=ref_rubber.start_y;
  var height=Math.abs(diff_y);

  if (diff_y>0)
    height-=1;
  else if (diff_y<0)
    top+=diff_y;

//  debug.print("new top: " + top);
//  debug.print("new left: " + left);
//  debug.print("new height: " + height);
//  debug.print("new width: " + width);

  r.style.top=top+"px";
  r.style.left=left+"px";
  r.style.width=width+"px";
  r.style.height=height+"px";

	if (panels.currentTool.mapBackgroundMoves) {
		mapdiv.style.backgroundPosition=diff_x+"px "+diff_y+"px";
		mapdiv.style.backgroundRepeat="no-repeat";
		//mapdiv.style.cursor=panels.currentTool.cursorStyle;
	}
}

/****************************************************************************
 * stopRubber()
 * stops tracking the movement of the rubberband and records its ending
 * coordinates.
 *  
 * Note: this function is used as an event handler, and when assigned as an
 * event handler, any "this" reference in the function does not refer to the 
 * RefRubberBand object, but rather to the calling object. so any reference to the
 * RefRubberBand object should be qualified with the name of the global variable
 * (i.e. -- the "this" keyword cannot be used to refer to the RefRefRubberBand
 * object).
 ***************************************************************************/ 
RefRubberBand.prototype.stopRubber=function(evt) {
  var r = ref_rubber.div;

  //Reset and undisplay
  r.style.height = "0px";
  r.style.width = "0px";
  r.style.top = "0px";
  r.style.left = "0px";
  r.style.display = "none";
  
  // remove our event handlers
  unregisterEvent(document,'onmousemove',ref_rubber.moveRubber);
  unregisterEvent(document,'onmousedown',ref_rubber.moveRubber);
  unregisterEvent(document,'onmouseup',ref_rubber.stopRubber);

//  var theQuery = ref_rubber.queryString();
//  ref_rubber.calculateCoordinates();
/*
  if (panels.currentTool.selectionHandler && panels.currentTool.selectionHandler.length>0) {
    eval(panels.currentTool.selectionHandler+"();");
  }
*/
}

/****************************************************************************
 * calculateCoordinates()
 * calculates the coordinates on the map image (in pixels) of the rubberband's
 * last bounding action. this may be a single point or 2 points that define
 * a bounding box (upper left and lower right). 
 ***************************************************************************/ 
RefRubberBand.prototype.calculateCoordinates=function() {
  /*************************************************************************
   * mapserver expects bounding box extents to be in the form of 2 points
   * whose coordinates describe the lower left point and upper right point as
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

  //Get the style of the parent container of ref rubber band
  var rub_par_style = ref_rubber.par_div.style;

  var max_left = parseInt(rub_par_style.left);
  var max_top = parseInt(rub_par_style.top);
  var max_height = parseInt(rub_par_style.height);
  var max_width = parseInt(rub_par_style.width);
  var max_right = max_left + max_width
  var max_bottom = max_top - max_height;

  //verify bounding coordinates are within the reference map image
  if (this.start_x > max_left && this.start_x < max_right &&
      this.start_y > max_top && this.start_y < max_bottom &&
      this.mouse_x > max_left && this.mouse_x < max_right &&
      this.mouse_y > max_bottom && this.mouse_y < max_bottom) {
    
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
RefRubberBand.prototype.queryString=function(queryType) {

  //Calculate new extents of map image using the reference image and
  //the newly formed rubber band box within it 

  //(Borrowed from OSCDL, DOES NOT WORK YET!)
  var r, qs;
  r = document.getElementById('map_overview_box');
  map_overview = document.getElementById('map_overview');
  if(r.style.left && r.style.height && r.style.display != "none") {
    scaleX = map.getWidth() / parseInt(map_overview.clientWidth);
    scaleY = map.getHeight() / parseInt(map_overview.clientHeight);
    if(document.all){
      var windowHeight = parseInt(document.body.clientHeight);
    }else{
      var windowHeight = parseInt(window.innerHeight);
    }
    //  var box_x1 = Math.abs((parseInt(r.style.left) - parseInt(map_overview.clientWidth) ));                                                                    
    var box_x1 = Math.abs(parseInt(r.style.left) * scaleX);
    //      var box_y1 = Math.abs((parseInt(r.style.top) - parseInt(windowHeight - map_overview.clientHeight) + parseInt(r.style.height)) * scaleY);                  
    var box_y1 = Math.abs(parseInt(r.style.top) * scaleY);
    var box_x2 = Math.abs(box_x1 + (parseInt(r.style.width) * scaleX));
    var box_y2 = Math.abs(box_y1 + (parseInt(r.style.height) * scaleY));

      // check for zoom point vs. select box                                                                                                                      
    if ((parseInt(r.style.width) < 4 && parseInt(r.style.height) < 4)) {
      qs = "zoompoint[x]="+box_x1+"&zoompoint[y]="+box_y1+"&" + "&zoom_factor=" + document.getElementById("zoomOut_scale").value + "&";
    }
    else {
        qs =
        "&select_box[x2]="+parseInt(box_x1)+
        "&select_box[y2]="+parseInt(box_y1)+
        "&select_box[x1]="+parseInt(box_x2)+
        "&select_box[y1]="+parseInt(box_y2)+"&";
    }
    return qs;
  }

/*
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
*/
}
