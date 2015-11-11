 /***********************************************************************
 *
 * @file          tabs.js
 *
 * $Id: tabs.js 84 2006-12-15 17:06:03Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/tabs.js $
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
 * This file contains 2 objects: TabGroup and TabItem. TabItem holds the
 * state of a given map tab, and TabGroup holds and manages the TabItems.
 ***************************************************************************/

/***************************************************************************
* TabItem object
*     Note: The functions here operate under the assumption that
*         TabItem.tabElements[0] is a div containing all of the tab's
*         elements (tab text, backgrounds, etc.)
**************************************************************************/
function TabItem (name,id,width,panel,closedY,openY,panelWidth,ordinal) {
  /* tabElements: an array of objects that are all part of the same tab. you
  *  should have a css class for each element to function to its fullest.
  *  (named "on" and "off, see below) */  
	this.tabElements=new Array();
	this.name=name;          // name of the tab and what is displayed on the tab
	this.id=id;              // element id of the tab
	this.ordinal=ordinal;    // index in the tab group
	this.isOpen=this.isFloating=false;
	this.closedY=closedY;    // base offset value of the bottom of the tab when
                           // in the closed position
	this.openY=openY;        // base offset value of the bottom of the tab in the
                           // open position
	this.width=width;        // width of the tab
  this.left=0;             // tab's left position
	this.panelWidth=panelWidth;  // width of the tab's panel
	this.panel=panel;        // panel object (a div element)
  return this;
}

/****************************************************************************
* setLeft: moves the tab and its panel to a given left position
****************************************************************************/ 
TabItem.prototype.setLeft=function(tabLeft,groupLeft) {
  document.getElementById(this.id).style.left=tabLeft+"px";
  this.left=tabLeft;
  this.panel.style.left=(tabLeft+groupLeft)+"px";
}

/****************************************************************************
* TabGroup object
*     doSlide:
*         true: do "sliding drawer" effect when opened
*     additional (optional )arguments should be TabItem objects that can be
*         added at the time the TabGroup object is created
****************************************************************************/
function TabGroup(doSlide) {
	this.effect = doSlide;
	this.tabItems = new Array();
	this.activeTabItem = null;
  this.left = parseInt(document.getElementById("map_tabs").style.left);
  
	for (var i=1; i < arguments.length; ++i ) {
		this.tabItems[i-1] = arguments[i];
	}
	return this;
}

/****************************************************************************
* TabGroup.tabIsOpen
*     tabId: the id of a tab or a tab's panel
*
* returns true if the tab specified by tabId is in an open state.
* Note: only 1 attached tab can be open at a time
****************************************************************************/
TabGroup.prototype.tabIsOpen=function(tabId) {
  // if this is a tab panel id, extract the tabId from it
  var idx=tabId.lastIndexOf("_panel");
  if (idx>0) tabId=tabId.substring(0,idx);
  
  if (this.activeTabItem && this.activeTabItem.id==tabId) {
    return this.activeTabItem.isOpen;
  }
  return false;
}

/****************************************************************************
* TabGroup.tabClick ( string tabId )
* 
* opens or closes a tab, depending on it's current state
****************************************************************************/
TabGroup.prototype.tabClick=function (tabId) {
  // if this is a tab panel id, extract the tabId from it
  var idx=tabId.lastIndexOf("_panel");
  if (idx>0) tabId=tabId.substring(0,idx);
  
  if (this.activeTabItem && this.activeTabItem.id==tabId) {
    this.closeTab(this.activeTabItem);
  } else {
    if (this.activeTabItem && this.activeTabItem.isOpen) {
      this.closeTab(this.activeTabItem);
    }
    for (var i=0;i<this.tabItems.length;++i) {
      if (this.tabItems[i].id==tabId) {
        this.openTab(this.tabItems[i]);
        return;
      }
    }
  }
}

/****************************************************************************
* TabGroup.closeTab(TabItem object)
* 
* closes the TabItem object
****************************************************************************/
TabGroup.prototype.closeTab=function(theTab) {
  if (theTab==null) return;
  if (!theTab.isOpen) return;
  
  var tabElems=new Array();
  for (var i=0;i<theTab.tabElements.length;++i) {
    tabElems[i]=document.getElementById(theTab.tabElements[i]);
  }
  
  if (this.effect) {
	  var tabPos = parseInt(tabElems[0].style.bottom);
	  for ( i=0; tabPos >= 0; ++i) {
	  	tabElems[0].style.bottom = tabPos + "px";
	  	theTab.panel.style.height = tabPos + "px";
	  	--tabPos;
	  }
  }
  
	// just for good measure...
	tabElems[0].style.bottom = 0 + "px";
	theTab.panel.style.height = 0 + "px";

	for (var i=0; i < tabElems.length; ++i) {
		tabElems[i].className = "off";
	}

	tabElems[0].style.zIndex = 100 + theTab.ordinal;
	theTab.panel.style.zIndex = 50 + theTab.ordinal;
	theTab.panel.style.display = "none";
  theTab.isOpen = false;
  
	this.activeTabItem = null;
}

/****************************************************************************
* TabGroup.openTab(TabItem object)
* 
* opens the TabItem object
****************************************************************************/
TabGroup.prototype.openTab=function(theTab) {
  if (theTab==null) return;
  if (theTab.isOpen) return;
  var tabElems=new Array();
  for (var i=0;i<theTab.tabElements.length;++i) {
    tabElems[i]=document.getElementById(theTab.tabElements[i]);
  }
  
  theTab.panel.style.display = "block";
  theTab.panel.style.width=theTab.panelWidth+"px";
  if (this.effect) {
	  var numSteps = Math.abs(theTab.openY - theTab.closedY);
	  var tabPos = theTab.closedY;

	  for ( i=0; i < numSteps; ++i) {
		  tabElems[0].style.bottom = tabPos + "px";
		  theTab.panel.style.height = tabPos + "px";
		  ++tabPos;
	  }
  } else {
    var tabPos = theTab.closedY + Math.abs(theTab.openY - theTab.closedY);
    tabElems[0].style.bottom = tabPos + "px";
		theTab.panel.style.height = tabPos + "px";
  }
  
	for (var i=0; i < tabElems.length; ++i) {
		tabElems[i].className = "on";
	}

	tabElems[0].style.zIndex = 100;
	theTab.panel.style.zIndex = 101;
  theTab.isOpen = true;
  
	this.activeTabItem = theTab;
}

/************************************************************************
* TabGroup.addTab
*   appends a TabItem object to the TabGroup
************************************************************************/
TabGroup.prototype.addTab=function(name,tabWidth,panelWidth,panelHeight) {
  var count=this.tabItems.length;
  var id="infoTab_"+count;
  
  // create the tab's div element
  var tab=document.createElement("div");
  tab.id=id;
  var html="<ul id=\""+id+"_ul\">";
  html+="<li class=\"off\" id=\""+id+"_li\" onclick=\"infoTabs.tabClick('"+id+"');\">";
  html+="<a class=\"off\" id=\""+id+"_a\">"+name+"</a></li></ul>";
  tab.innerHTML=html;
  document.getElementById("map_tabs").appendChild(tab);
  tab.style.width=tabWidth+"px";

  // create another div for the tab's panel
  var panel=document.createElement("div");
  panel.id=id+"_panel";
  panel.className="tab_panel";
  panel.style.position="absolute";
  panel.style.width=panelWidth+"px";
  
  // create a TabItem object to manage the state of the tab
  var newTab=new TabItem(name,id,tabWidth,panel,0,panelHeight,panelWidth,count);
  newTab.tabElements[0]=id;
  newTab.tabElements[1]=id+"_ul";
  newTab.tabElements[2]=id+"_li";
  newTab.tabElements[3]=id+"_a";
  this.tabItems.push(newTab);
  
  document.getElementById("body").appendChild(panel);
  this.positionTabs();
  return panel.id;
}

/************************************************************************
* TabGroup.removeTab()
*   removes a TabItem object from the TabGroup
************************************************************************/
TabGroup.prototype.removeTab=function(name) {
  var tab,tabNode,parentNode;
  for (var i=0;i<this.tabItems.length;++i) {
    tab=this.tabItems[i];
    if (tab.name==name) {
      // remove the tab
      tabNode=document.getElementById(tab.id);
      tabNode.parentNode.removeChild(tabNode);
      // remove the tab's panel
      tabNode=document.getElementById(tab.panel.id);
      tabNode.parentNode.removeChild(tabNode);
      // remove the tab object
      this.tabItems.splice(i,1);
      this.positionTabs();
      break;
    }
  }
}

/************************************************************************
* TabGroup.removeAllTabs()
*   hmm ... not sure what this function does. any ideas?
************************************************************************/
TabGroup.prototype.removeAllTabs=function() {
  var tab,tabNode,parentNode;
  for (var i=this.tabItems.length-1;i>=0;--i) {
    tab=this.tabItems[i];

    //Do not remove the debug or messages tabs
    if (tab.name.search(/debug/gi)!=INVALID)
      continue;
    if (tab.name.search(/messages/gi)!=INVALID)
      continue;

      // remove the tab
      tabNode=document.getElementById(tab.id);
      tabNode.parentNode.removeChild(tabNode);
      // remove the tab's panel
      tabNode=document.getElementById(tab.panel.id);
      tabNode.parentNode.removeChild(tabNode);
      // remove the tab object
      this.tabItems.splice(i,1);
  }
}

/************************************************************************
* TabGroup.positionTabs()
*   rearranges all tabs (usually used when a tab is removed or added)
************************************************************************/
TabGroup.prototype.positionTabs=function() {
  var tab,left=0;
  for (var i=0;i<this.tabItems.length;++i) {
    tab=this.tabItems[i];
    tab.setLeft(left,this.left);
    left+=tab.width;
  }
}
