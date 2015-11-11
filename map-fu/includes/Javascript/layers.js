 /***********************************************************************
 *
 * @file          layers.js
 *
 * $Id: layers.js 47 2006-11-07 00:44:15Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/layers.js $
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
 * This file contains 3 objects: LayerState, MapLayer and MapLayerGroup. The
 * MapLayer and MapLayerGroup object simply hold state information for a
 * layer or layer group, respectively. The LayerState object manages the 
 * addition/removal and displaying of the groups and their layers. Additionally,
 * each object is responsible for drawing itself and any subordinates it may
 * be managing.   
 ***************************************************************************/

/***********************************************************************
* MapLayerGroup - the object that manages the layers for a given group
***********************************************************************/
function MapLayerGroup() {
  this.name="";
  this.id="";
  this.index=INVALID;
  this.isOn=true;
  this.isOpen=true;
  this.layers=new Array();
  return this;
}

/***********************************************************************
* addLayer()
* appends a layer object to the group's list of layers
***********************************************************************/
MapLayerGroup.prototype.addLayer=function(name,on,visible,togglable) {
  var layerObj=new MapLayer();
  lindex=this.layers.length;
  layerObj.name=name;
  if (this.index!=INVALID)
    layerObj.id="layer_"+this.index+"_"+lindex;
  else
    layerObj.id="layer__"+lindex;
  layerObj.group=this.name;
  layerObj.groupIndex=this.index;
  layerObj.isOn=on;
  layerObj.isVisible=visible;
  layerObj.canToggleOnOff=togglable;
  this.layers.push(layerObj);
}

/***********************************************************************
* layerExists()
* returns true if a layer by the name of 'name' exists in the group's
* list of layers.
***********************************************************************/
MapLayerGroup.prototype.layerExists=function(name) {
  for (var i=0;i<this.layers.length;++i)
    if (name==this.layers[i].name) return true;
  return false;
}

/***********************************************************************
* asHTML()
* returns HTML of the layer group and each of its layers.
***********************************************************************/
MapLayerGroup.prototype.asHTML=function() {
  var html,layerObj,input,img,src,display;
  var stl="width:"+(panels_width-panels_margin*2)+"px;"+
    "margin-left:"+(panels_margin-1)+"px;";
  html="<table class=\"group\" id=\""+this.id+"\" style=\""+stl+"\">\n";
  if (this.layers.length>0) {
    input="<input type=\"checkbox\" id=\""+this.id+"_input\""+
      " onclick=\"panels.toggleGroupLayers(event,'"+this.id+"');\""+
      " checked=\"checked\" /> ";
    src=(layer_groups_open)?"group_open.png":"group_closed.png";
    img="<img id=\""+this.id+"_img\" src=\"images/"+src+"\" "+
      "onclick=\"panels.onGroupClick('"+this.id+"');\" />";
  } else {
    input=img=" ";
  }
  html+="<tr><td onclick=\"panels.onGroupClick('"+this.id+"');\">"+
    "<table width=\"100%\"><tr><td class=\"group_item\">"+input+
    "</td><td class=\"group_item\">"+img+"</td><th>"+this.name+
    "</th></tr></table>\n</td></tr>\n";
  display=(layer_groups_open)?"block":"none";
  html+="<tr><td><table class=\"group_layers\" id=\""+this.id+
    "_layers\" style=\"display:"+display+";\">\n";
  for (var i=0;i<this.layers.length;++i) {
    layerObj=this.layers[i];
    html+=layerObj.asHTML();
  }
  html+="</table>\n</td></tr>\n</table>\n";
  return html;
}

/***********************************************************************
* MapLayer - the object that manages a given layer
***********************************************************************/
function MapLayer() {
  this.name="";
  this.id="";
  this.isOn=true;
  this.isVisible=true;
  this.canToggleOnOff=true;
  this.group="";
  this.groupIndex=INVALID;
  return this;
}

/***********************************************************************
* asHTML()
* returns HTML of a given layer.
***********************************************************************/
MapLayer.prototype.asHTML=function() {
  var html="";
  if (this.isVisible) {
    html+="<tr><td class=\"layer_input\">";
    html+="<input type=\"checkbox\" id=\""+this.id+"_input\"";
    if (this.canToggleOnOff)
      html+=" onclick=\"panels.onLayerClick(event,'"+this.id+"');\"";
    else
      html+=" disabled=\"disabled\"";
    if (this.isOn)
      html+=" checked=\"checked\" />";
    else
      html+=" />";
    html+="</td>\n<td>";
    if (this.canToggleOnOff)
      html+="<label for=\""+this.id+"_input\">"+this.name+"</label>";
    else
      html+=this.name;
    html+="</td></tr>\n";
  }
  return html;
}

/***********************************************************************
* LayerState - an object that manages all groups and layers
***********************************************************************/
function LayerState() { 
  this.layerdiv=document.getElementById("groups");
  return this;
}

/***********************************************************************
* init()
* initializes the layerstate object, used when a map is initially created
* and the mapfile is inspected.
***********************************************************************/
LayerState.prototype.init=function() {
  this.layerdiv.innerHTML="";
  this.groups=new Array();
  this.nongroup_layers=new MapLayerGroup();
  this.layersOn=new Array();
  this.layersVisible=new Array();
  this.layersTogglable=new Array();
  this.isInitialized=false;
}

/***********************************************************************
* addGroup()
* adds a group object to the list of known layer groups.
***********************************************************************/
LayerState.prototype.addGroup=function(name) {
  var g=new MapLayerGroup();
  g.name=name;
  g.index=this.groups.length;
  g.id="group_"+g.index;
  g.isOpen=layer_groups_open;
  this.groups.push(g); 
  return g;
}

/***********************************************************************
* addLayer()
* adds a layer object to a group, adding the group if it doesn't already
* exist.
***********************************************************************/
LayerState.prototype.addLayer=function(name,group,on,visible,togglable) {
  if (group && group.length>0) {
    // find the group this layer is to be added to
    var g=this.getGroup(group);
    if (g==null) {
      // group doesn't exist yet
      g=this.addGroup(group);
    }
    if (!g.layerExists(name))
      g.addLayer(name,on,visible,togglable);
  } else {
    // no group, so add to nongroup layers
    if (!this.nongroup_layers.layerExists(name))
      this.nongroup_layers.addLayer(name,on,visible,togglable);
  }
}

/***********************************************************************
* getLayer()
* returns a layer object, if it exists.
***********************************************************************/
LayerState.prototype.getLayer=function(layer_id) {
  var layerObj=null;
  var temp=layer_id.split("_");
  var gindex=temp[1];
  var lindex=temp[2];
  if (gindex.length>0) {
    gindex/=1;
    if (gindex>=0 && gindex<this.groups.length) {
      lindex/=1;
      if (lindex>=0 && lindex<this.groups[gindex].layers.length)
        layerObj=this.groups[gindex].layers[lindex];
    }
  } else {
    lindex/=1;
    if (lindex>=0 && lindex<this.nongroup_layers.layers.length)
      layerObj=this.nongroup_layers.layers[lindex];
  }
  return layerObj;
}

/***********************************************************************
* getGroup()
* returns a group object, if it exists.
***********************************************************************/
LayerState.prototype.getGroup=function(name) {
  for (var i=0;i<this.groups.length;++i) {
    if (name==this.groups[i].name)
      return this.groups[i];
  }
  return null;
}

/***********************************************************************
* getGroup()
* returns a group object, if it exists.
***********************************************************************/
LayerState.prototype.getGroupById=function(id) {
  var g=null;
  for (var i=0;i<this.groups.length;++i) {
    g=this.groups[i];
    if (id==g.id) break;
  }
  return g;
}

/***********************************************************************
* layerName()
* returns the name of a layer given its id.
***********************************************************************/
LayerState.prototype.layerName=function(layer_id) {
  var layerObj=this.getLayer(layer_id);
  return layerObj.name;
}

/***********************************************************************
* isOn()
* returns true if the layer is on (checked).
***********************************************************************/
LayerState.prototype.isOn=function(layer_id) {
  var layerObj=this.getLayer(layer_id);
  return layerObj.isOn;
}

/***********************************************************************
* isVisible()
* returns true if the layer is visible.
***********************************************************************/
LayerState.prototype.isVisible=function(layer_id) {
  var layerObj=this.getLayer(layer_id);
  return layerObj.isVisible;
}

/***********************************************************************
* canToggleOnOff()
* returns true if the layer can be toggled on/off (checked/unchecked).
* some layers are always on or always off.
***********************************************************************/
LayerState.prototype.canToggleOnOff=function(layer_id) {
  var layerObj=this.getLayer(layer_id);
  return layerObj.canToggleOnOff;
}

/***********************************************************************
* queryString()
* returns a string containing the names of all of the layers that are
* currently on.
***********************************************************************/
LayerState.prototype.queryString=function() {
  var layerQuery=new Array();
  var groupObj,layerObj;
  var k=0;
  for (var i=0;i<this.groups.length;++i) {
    groupObj=this.groups[i];
    for (var j=0;j<groupObj.layers.length;++j) {
      layerObj=groupObj.layers[j];
      if (layerObj.isOn) {
        layerQuery.push("layers["+k+"]="+escape(layerObj.name));
        ++k;
      }
    }
  }
  for (i=0;i<this.nongroup_layers.layers.length;++i) {
    layerObj=this.nongroup_layers.layers[i];
    layerQuery.push("layers["+k+"]="+escape(layerObj.name));
    ++k;
  }
  return layerQuery.join("&");
}

/***********************************************************************
* drawLayers()
* creates HTML of all the groups and their layers for display in the 
* groups panel.
***********************************************************************/
LayerState.prototype.drawLayers=function() {
  var html="";
  if (this.groups.length>0) {
    var groupObj;
    for (var i=0;i<this.groups.length;++i) {
      groupObj=this.groups[i];
      html+=groupObj.asHTML();
    }
  } else {
    html="<span class=\"text\" style=\"font-weight:bold;\">No selectable layers</span>";
  }
  this.layerdiv.innerHTML=html;
}
