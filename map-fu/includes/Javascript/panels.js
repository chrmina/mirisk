 /***********************************************************************
 *
 * @file          panels.js
 *
 * $Id: panels.js 86 2006-12-16 01:08:56Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/panels.js $
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
 * This file contains the Panels object, used in managing state of the panels
 * (layers, tools, datasources) and showing/hiding each appropriately. This
 * object also uses tool objects (found in the tools.js file), and layer and
 * layer group objects (found in layers.js).
 ***************************************************************************/
 
/*******************************************************************
* Panels - the object that manages the tools and layers divs
*******************************************************************/
function Panels() {
  this.tools=new Array();
	this.currentToolId="";
	this.currentTool=null;
	this.currentPanel="";
	this.defaultToolId="";
	return this;
}

/***************************************************************************
 * init()
 * called when the panels object needs to be initialized, such as the document
 * onload event or when the mapfile changes.  
 ***************************************************************************/
Panels.prototype.init=function() {
	this.onPanelClick("tools");
}

/***************************************************************************
 * addTool()
 * adds a tool object to the list of known tools.  
 ***************************************************************************/
Panels.prototype.addTool=function(toolObj) {
  this.tools.push(toolObj);
}

/***************************************************************************
 * getTool()
 * searches for a tool object by the tool's document element id and returns
 * it if found (or null otherwise).  
 ***************************************************************************/
Panels.prototype.getTool=function(toolId) {
  var toolObj=null;
  for (var i=0;i<this.tools.length;++i) {
    if ((this.tools[i]).id==toolId) {
      toolObj=this.tools[i];
      break;
    }
  }
  return toolObj;
}
  
/***************************************************************************
 * onToolClick()
 * turns the current tool "off" and turns "on" the newly selected tool.  
***************************************************************************/
Panels.prototype.onToolClick=function(toolId) {
  if (this.currentToolId==toolId) return;
  
  // find the object for the tool in question
  var toolObj=this.getTool(toolId);
  if (!toolObj) {
    error.print('unable to find tool '+toolId);
    return;
  }
  
  // some tools can't be "current", like the scalebar
  if (toolObj.canBeCurrent) {
    var element;
    // first de-activate the old tool
    if (this.currentTool) {
      if (this.currentTool.lostFocusHandler.length>0)
        eval(this.currentTool.lostFocusHandler+"();");
      element=document.getElementById("tool_"+this.currentToolId);
      element.className="tool_off";
      element.style.backgroundColor='#fff';
    }
    
    // activate the newly selected tool
    element=document.getElementById("tool_"+toolId);
    element.className="tool_on";
    element.style.backgroundColor='#ddd';
    
    if (toolObj.cursor)
      glassdiv.style.cursor=toolObj.cursor;
    else
      glassdiv.style.cursor="default";
    
    // synch our pointer to the currentToolId
    this.currentToolId=toolId;
    this.currentTool=toolObj;
  }
}

/***************************************************************************
 * onPanelClick()
 * hides the currently viewed panel and shows the panel that was clicked on.  
 ***************************************************************************/
Panels.prototype.onPanelClick=function(new_panel) {
  if (this.currentPanel.length>0) {
		document.getElementById(this.currentPanel+"_panel").style.display="none";
		document.getElementById(this.currentPanel+"_li").className="off";
		document.getElementById(this.currentPanel+"_a").className="off";
  }
  
	var panel=document.getElementById(new_panel+"_panel");
	panel.style.display="block";
	document.getElementById(new_panel+"_li").className="on";
	document.getElementById(new_panel+"_a").className="on";

	this.currentPanel=new_panel;
}

/***************************************************************************
 * onLayerClick()
 * called when a layer checkbox is clicked, this function will uncheck the
 * group's checkbox if the layer's checkbox is unchecked.
 ***************************************************************************/
Panels.prototype.onLayerClick=function(evt,layer_id) {
  //Get the layer object
  var layerObj=currentLayerState.getLayer(layer_id);
  if (layerObj.canToggleOnOff) {
    //Get layer checkbox value from UI
    var isOn=document.getElementById(layer_id+"_input").checked;
    layerObj.isOn=isOn;
    //If a layer is now off, set its group checkbox to false... why??? 
    //what if the layer is turned back on?
    //if (!isOn)
    //  document.getElementById("group_"+layerObj.groupIndex+"_input").checked=false;

    if (update_map_on_layer_change)
      map.updateMap(null, true);
  }
  if (window.event)
    window.event.cancelBubble=true;
  else
    evt.stopPropagation();
}

/***************************************************************************
 * onGroupClick()
 * shows/hides the layers belonging to a given group.  
 ***************************************************************************/
Panels.prototype.onGroupClick=function(group_id) {
  var layers=document.getElementById(group_id+"_layers");
  var group=currentLayerState.getGroupById(group_id);
  var group_img=document.getElementById(group_id+"_img");
  if (layers && group) {
    group.isOpen=!group.isOpen;
    layers.style.display=(group.isOpen)?"block":"none";
    group_img.src=(group.isOpen)?"images/group_open.png":"images/group_closed.png";
  }
}

/***************************************************************************
 * toggleGroupLayers()
 * called when a layer group's checkbox is clicked, this function will check
 * all the layers in a group if the group's checkbox is checked.  
 ***************************************************************************/
Panels.prototype.toggleGroupLayers=function(evt,group_id) {
  var group_input=document.getElementById(group_id+"_input");
  if (group_input.checked) {
    var pos=group_id.lastIndexOf("_");
    var gindex=group_id.substring(pos+1,group_id.length);
    var layerObj;
    var groupObj=currentLayerState.groups[gindex];
    for (var i=0;i<groupObj.layers.length;++i) {
      layerObj=groupObj.layers[i];
      if (layerObj.canToggleOnOff) {
        document.getElementById(layerObj.id+"_input").checked=true;
        layerObj.isOn=true;
      }
    }
  }
  // stop event bubbling to keep the group's layers from being hidden/shown
  if (window.event)
    window.event.cancelBubble=true;
  else
    evt.stopPropagation();
}
