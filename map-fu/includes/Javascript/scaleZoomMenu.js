 /***********************************************************************
 *
 * @file          scaleZoomMenu.js
 *
 * $Id: scaleZoomMenu.js 47 2006-11-07 00:44:15Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/scaleZoomMenu.js $
 *
 * @project       Map-Fu
 *
 * This project was developed as part of the Oregon Sustainable
 * Community Digital Library (OSCDL) by Academic & Research Computing
 * at Portland State University with support by Oregon State
 * Library grants 245020, 245021.  Special thanks to Rose Jackson and 
 * the OSCDL project.
 *
 * @contributors  Tim Welch
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
 * The ScaleZoomMenu object represents a scale zoom selector which is used in 
 * the scale zoom tool.  It's used to jump to a given extent.  It is in the
 * form of an HTML drop down menu.
 * 
 * This object is created after the map is initially created and each time the
 * extents of the base map data changes.  The drop down menu is updated if 
 * the + or - zoom buttons are used to reflect the current map extent.
 ***************************************************************************/

/****************************************************************************
* ScaleZoomMenu object
*     menu_id: id of menu element
*     clickHandler: name of function to execute onClick
*     rangeType: defines type of range that zoom scale should be
*     dispType: defines how zoom scale should be displayed
*     jsonScaleVals: serialized array of zoom scale values
****************************************************************************/
  function ScaleZoomMenu(menuId, clickHandler, rangeType, dispType, jsonScaleVals) {

  this.menuId = menuId;
  this.clickHandler = clickHandler;
  this.rangeType = rangeType;
  this.dispType = dispType;

  // master zoom scale array.  A subset of this is used at any given time
  // @todo should be provided by ScaleZoom.php
  this.fullScaleVals = eval('(' + jsonScaleVals + ')');
  this.fullNumScales = this.fullScaleVals.length;
  this.scaleArr = Array();

  //Maintains current and possible map extents for current zoom scale
  this.curMin = null;
  this.curMax = null;
  this.curScale = null;
}

/****************************************************************************
* getScaleMin() - returns the minimum extent of current zoom scale
****************************************************************************/
ScaleZoomMenu.prototype.getScaleMin = function () {
  return this.curMin;
}

/****************************************************************************
* getScaleMax() - returns the maximum extent of current zoom scale
****************************************************************************/
ScaleZoomMenu.prototype.getScaleMax = function () {
  return this.curMax;
}

/****************************************************************************
* getScaleCur() - returns the current extent of current zoom scale
****************************************************************************/
ScaleZoomMenu.prototype.getScaleCur = function () {
  return this.curScale;
}

/****************************************************************************
* getScaleMin() - returns the current array of map extents used by zoom scale
****************************************************************************/
ScaleZoomMenu.prototype.getScaleArr = function () {
  return this.scaleArr;
}

/***************************************************************************
 * updateScale()
 * Calls for creation of array of zoom scale values and regeneration of the 
 * zoom scale menu using that array.
 ***************************************************************************/
ScaleZoomMenu.prototype.updateScale = function () {
  this.curMin = map.minScale;
  this.curMax = map.maxScale;
  this.genScaleArr();
  this.updateScaleMenu();
}

/***************************************************************************
 * genScaleArr()
 * Generates an array of zoom scale values using the current min and max map
 * extents and the master array of zoom values. 
 ***************************************************************************/
ScaleZoomMenu.prototype.genScaleArr = function () {
  switch(rangeType) {
  case 'canned':
    // Generate scale array using canned values
    
    this.scaleArr[0] = this.curMax;  // Insert min extent as first array val
    var curIndex = 1;        // Current empty slot in the new scale array we're building
    var i=1;                 // Current index in master scale array

    // Fill in the middle of the scale array
    for (i=1;i<=this.fullNumScales;++i) {
      curVal = this.fullScaleVals[i-1];
      if (curVal > this.curMin && curVal < this.curMax) {
        this.scaleArr[curIndex] = curVal;
        curIndex++;
      }
    }        
    // Insert max as last array val
    this.scaleArr[curIndex] = this.curMin;
    break;
  }
}

/***************************************************************************
 * updateScaleMenu()
 * Regenerates the scale zoom menu given an array of zoom values
 ***************************************************************************/
ScaleZoomMenu.prototype.updateScaleMenu = function () {
  switch(scaleDispType) {
  case 'drop-down':
    var zoom_scale_menu = document.getElementById(this.menuId);
    //Reset the menu
    zoom_scale_menu.options.length=0
    //Rebuild the menu
    for (var i=0; i<this.scaleArr.length; ++i) {    
      // Create a new select option using Option constructor
      zoom_scale_menu.options[i]=new Option("1:"+addCommas(this.scaleArr[i]), this.scaleArr[i], false, false);
    }
    // Add an onchange event.  Creates a function pointer to call the click handler and builds the arguments
    var onchange_str = "var zoom_scale_menu = document.getElementById('" + this.menuId + "');";
    onchange_str += this.clickHandler + "((zoom_scale_menu.options[(zoom_scale_menu.options).selectedIndex]).value);";
    zoom_scale_menu.onchange = new Function(onchange_str);
    break;
  }
}

/***************************************************************************
 * setZoomMenuVal()
 * Shifts the scale zoom menu selectedIndex up or down 1.
 ***************************************************************************/
ScaleZoomMenu.prototype.setZoomMenuVal = function (direction) {
  var menu = document.getElementById(this.menuId);
  switch(direction) {
  case 'up':    
    if (menu.selectedIndex != 0)
      menu.selectedIndex -= 1;
    break;
  case 'down':
    if (menu.selectedIndex != menu.length)
      menu.selectedIndex += 1;
    break;
  }
}

/***************************************************************************
 * getZoomMenuVal()
 * Returns the scale zoom menu DOM element.
 ***************************************************************************/
ScaleZoomMenu.prototype.getZoomMenuVal = function () {
  var menu = document.getElementById(this.menuId);
  return menu.value;
}