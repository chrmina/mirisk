 /***********************************************************************
 * @file          index.php
 *
 * $Id: floater.js 47 2006-11-07 00:44:15Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/floater.js $
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
 * This file contains the 2 classes: the Floater and RegisteredFloaters. A
 * Floater object is intended to allow some element to be able to move (float)
 * around the screen, and this object maintains the state of the floating
 * element and tracks its movement. The RegisteredFloaters object is a global
 * object declared in main.js and used as a collection of existing floater
 * objects.  
 ***************************************************************************/

/*******************************************************************
* Floater - the object that manages a given element (usually a div).
*******************************************************************/
function Floater(id) {
  this.id=id;
  this.index=INVALID;
  this.isVisible=false;
  this.left=this.top=0;
  this.offsetX=this.offsetY=0;
  this.zIndex=200;
  this.me=document.getElementById(id);
  this.startEventIds=new Array();
  this.startEvents=new Array();
  this.startEventHandlers=new Array();
  this.trackingEventIds=new Array();
  this.trackingEvents=new Array();
  this.trackingEventHandlers=new Array();
  this.stopEventIds=new Array();
  this.stopEvents=new Array();
  this.stopEventHandlers=new Array();
  return this;
}

/***************************************************************************
 * init()
 * an element can be initialized with optional starting left and top positions,
 * as well as offsets for the positions of top and left when being moved    
 * -- if left and top are supplied, both should be supplied, and in that order
 * -- one or both offsets can be supplied, they will be used to calculate the
 *    position to move to when moving
 * any optional arguments are expected in this order:
 *    left, top, offsetX, offsetY    
 **************************************************************************/
Floater.prototype.init=function() {
  if (arguments.length>=2) {
    if (arguments.length==4)
      this.setOffsets(arguments[2],arguments[3]);
    this.move(arguments[0],arguments[1]);
  } else {
    // left
    if (this.me.style && this.me.style.left)
      this.left=parseInt(this.me.style.left);
    else if (this.me.offsetLeft)
      this.left=this.me.offsetLeft;
    // top
    if (this.me.style && this.me.style.top)
      this.top=parseInt(this.me.style.top);
    else if (this.me.offsetTop)
      this.top=this.me.offsetTop;
  }
}

/***************************************************************************
 * addStartEvent()
 * attaches a function to an event for a given element (not necessarily the
 * element that the floater manages) that is called to start tracking
 * movement of the floating element. 
 ***************************************************************************/
Floater.prototype.addStartEvent=function(elementId,eventType) {
  var etype=this.determineEventType(elementId,eventType);
  if (!etype) {
    error.print("Unable to add start event "+eventType+" for "+elementId);
    return;
  }
  // set the starting event handler
  var element,eventString;
  switch (elementId.toLowerCase()) {
    case "document": element=document; break;
    case "window": element=window; break;
    case "self": element=self; break;
    default: element=document.getElementById(elementId); break;
  }
  if (element) {
    var index=this.index;
    if (arguments.length==4)
      var func=arguments[3];
    else
      var func=function(event) {floaters.getFloater(index).startTracking(event);};
    registerEvent(element,etype,func);
  } else {
    debug.print("Unable to set starting event "+eventType+" for "+elementId);
  }
}

/***************************************************************************
 * addTrackingEvent()
 * stores a function for an event for a given element (not necessarily the
 * element that the floater manages) that gets called to maintain movement
 * tracking of the floating element. 
 ***************************************************************************/
Floater.prototype.addTrackingEvent=function(elementId,eventType) {
  var etype=this.determineEventType(elementId,eventType);
  if (!etype) {
    error.print("Unable to add tracking event "+eventType+" for "+elementId);
    return;
  }
  this.trackingEventIds.push(elementId);
  this.trackingEvents.push(etype);
  var index=this.index;
  if (arguments.length==4)
    this.trackingEventHandlers.push(arguments[3]);
  else
    this.trackingEventHandlers.push(function(event) {floaters.getFloater(index).moveTracking(event);});
}

/***************************************************************************
 * addStopEvent()
 * stores a function for an event for a given element (not necessarily the
 * element that the floater manages) that gets called to stop the movement
 * tracking of the floating element.
 ***************************************************************************/
Floater.prototype.addStopEvent=function(elementId,eventType) {
  var etype=this.determineEventType(elementId,eventType);
  if (!etype) {
    error.print("Unable to add stop event "+eventType+" for "+elementId);
    return;
  }
  this.stopEventIds.push(elementId);
  this.stopEvents.push(etype);
  var index=this.index;
  if (arguments.length==4)
    this.stopEventHandlers.push(arguments[3]);
  else
    this.stopEventHandlers.push(function(event) {floaters.getFloater(index).stopTracking(event);});
}

/***************************************************************************
 * setOffsets()
 * sets the offset of the floating element. the offsets are used to position
 * the element relative to it's top and left positions. for example, if the
 * element is a div and a click event occurs in the middle of it, the offsets
 * would be the difference between the click's X and the element's left, and
 * the difference between the click's Y and the element's top.
 ***************************************************************************/
Floater.prototype.setOffsets=function(x,y) {
  this.offsetX=x;
  this.offsetY=y;
}

/***************************************************************************
 * show()
 * makes the floating element visible.
 ***************************************************************************/
Floater.prototype.show=function() {
  this.me.style.zIndex=this.zIndex;
  this.me.style.display="block";
  this.isVisible=true;
}

/***************************************************************************
 * hide()
 * makes the floating element invisible.
 ***************************************************************************/
Floater.prototype.hide=function() {
  this.me.style.zIndex=0;
  this.me.style.display="none";
  this.isVisible=false;
}

/***************************************************************************
 * move()
 * moves the floating element to the specified left and top.
 ***************************************************************************/
Floater.prototype.move=function(left,top) {
  if (this.offsetX!=0) left+=this.offsetX;
  if (this.offsetY!=0) top+=this.offsetY;
  this.me.style.left=left+"px";
  this.me.style.top=top+"px";
  this.left=left;
  this.top=top;
}

/***************************************************************************
 * startTracking()
 * tells the floater object to attach events to track the movement of the
 * floating element and to stop tracking it's movement.
 ***************************************************************************/
Floater.prototype.startTracking=function() {
  if (arguments.length==1) {
    var evt = arguments[0] || window.event;
    this.move(evt.clientX,evt.clientY);
  }
  // set the event handlers
  var element;
  for (var i=0;i<this.trackingEvents.length;++i) {
    switch (this.trackingEventIds[i].toLowerCase()) {
      case "document": element=document; break;
      case "window": element=window; break;
      case "self": element=self; break;
      default: element=document.getElementById(this.trackingEventIds[i]); break;
    }
    if (element) {
      registerEvent(element,this.trackingEvents[i],this.trackingEventHandlers[i]);
    } else {
      debug.print("Unable to set tracking event "+this.trackingEvents[i]+" for "+this.trackingEventIds[i]);
    }
  }
  for (var i=0;i<this.stopEvents.length;++i) {
    switch (this.trackingEventIds[i].toLowerCase()) {
      case "document": element=document; break;
      case "window": element=window; break;
      case "self": element=self; break;
      default: element=document.getElementById(this.trackingEventIds[i]); break;
    }
    if (element) {
      registerEvent(element,this.stopEvents[i],this.stopEventHandlers[i]);
    } else {
      debug.print("Unable to set stop event "+this.stopEvents[i]+" for "+this.stopEventIds[i]);
    }
  }
}

/***************************************************************************
 * moveTracking()
 * moves the floating element to the event object's X and Y.
 ***************************************************************************/
Floater.prototype.moveTracking=function(evt) {
  var evt = evt || window.event;
  this.move(evt.clientX,evt.clientY);
}

/***************************************************************************
 * stopTracking()
 * tells the floater object to unattach any events associated with tracking
 * the movement of the floating element.
 ***************************************************************************/
Floater.prototype.stopTracking=function(evt) {
  var element;
  // unset the event handlers
  for (var i=0;i<this.trackingEvents.length;++i) {
    switch (this.trackingEventIds[i].toLowerCase()) {
      case "document": element=document; break;
      case "window": element=window; break;
      case "self": element=self; break;
      default: element=document.getElementById(this.trackingEventIds[i]); break;
    }
    if (element) {
      unregisterEvent(element,this.trackingEvents[i],this.trackingEventHandlers[i]);
    } else {
      debug.print("Unable to remove tracking event "+this.trackingEvents[i]+" for "+this.trackingEventIds[i]);
    }
  }
  for (var i=0;i<this.stopEvents.length;++i) {
    switch (this.stopEventIds[i].toLowerCase()) {
      case "document": element=document; break;
      case "window": element=window; break;
      case "self": element=self; break;
      default: element=document.getElementById(this.stopEventIds[i]); break;
    }
    if (element) {
      unregisterEvent(element,this.stopEvents[i],this.stopEventHandlers[i]);
    } else {
      debug.print("Unable to remove stop event "+this.stopEvents[i]+" for "+this.stopEventIds[i]);
    }
  }
}

/***************************************************************************
 * determineEventType()
 * not surprisingly, the event models for IE and W3C are different. this
 * routine looks at the event type (a string) and modifies it if needed to
 * match the browser's event model. IE expects the named event to have an
 * 'on' prefix, while W3C expects the named event NOT to have an 'on' prefix.
 * Example:
 *   IE:    element.attachEvent('onclick',handler);
 *   W3C:   element.addEventListener('click',handler,false);
 * this routine allows the user to specify the event type with or without the
 * 'on' prefix.    
 ***************************************************************************/
Floater.prototype.determineEventType=function(element,eventType) {
  var e=null,t=null;
  switch (element.toLowerCase()) {
    case "document": e=document; break;
    case "window": e=window; break;
    case "self": e=self; break;
    default: e=document.getElementById(element); break;
  }
  if (e)
    t=determineEventType(e,eventType);
  else
    debug.print("Unable to locate element '"+element+"'");
  return t;
}

/*******************************************************************
* RegisteredFloaters - a collection of Floater objects.
*******************************************************************/
function RegisteredFloaters() {
  this.registered=new Array();
  return this;
}

/*******************************************************************
* addFloater()
* hmm ... i think it adds a Floater object to the collection.
*******************************************************************/
RegisteredFloaters.prototype.addFloater=function(floater) {
  floater.index=this.registered.length;
  this.registered.push(floater);
}

/*******************************************************************
* removeFloater()
* hmm ... pretty sure this removes a Floater object from the
* collection.
*******************************************************************/
RegisteredFloaters.prototype.removeFloater=function(index) {
  if (index>=0 && index<=this.registered.length-1) {
    this.registered.splice(index,1);
    for (var i=index;i<this.registered.length;++i)
      this.registered[i].index--;
  }
}

/*******************************************************************
* getFloater()
* hmm ... dunno what this one does?!
*******************************************************************/
RegisteredFloaters.prototype.getFloater=function(index) {
  if (index>=0 && index<=this.registered.length-1)
    return this.registered[index];
  return null;
}
