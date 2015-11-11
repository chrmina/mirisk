<?php
 /***********************************************************************
 * @file          ScaleZoom.php
 *
 * $Id: ScaleZoom.php 62 2006-11-14 22:10:14Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/ScaleZoom.php $
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
 * This file contains the class ScaleZoom. It is intended to provide the look
 * and functionality of an interface tool that allows the user to easily
 * zoom in or out to a predefined extent.
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/
require_once($abstract_path."Tool.php");
require_once($php_path."JSON.php");

class ScaleZoom extends Tool {
  private $scaleDispType;  //Type of scale to display
  private $rangeType;  //Type of scale range to use
  
  function __construct(&$group) {
    global $image_path;
    $this->name="Scale Zoom";
    $this->id="scale_zoom";  //id of zoom scale div
    $this->menu_id="zoom_scale_selector";  //id of zoom scale menu within div
    $this->description="Use this tool to zoom to a preset scale";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path;
    $this->type=CHANGE_EXTENT_TOOL;
    $this->clickHandler="ScaleZoomClick";
    $this->scaleDispType="drop-down";
    $this->rangeType="canned";
    $this->fullScaleVals = array(100000000, 50000000, 25000000, 10000000, 5000000, 
                            2500000, 1000000, 500000, 250000, 200000, 100000,
                            50000, 25000, 10000, 5000);

    $json=&new Services_JSON();
    $this->jsonScaleVals = $json->encode($this->fullScaleVals);
  }
  
  function __destruct() {
  }
  
  public function inlineJavascript() {

    $js=<<<EOT
    var menu_id = ("{$this->menu_id}");
    var clickHandler = "{$this->clickHandler}";
    var rangeType = "{$this->rangeType}";
    var scaleDispType = "{$this->scaleDispType}";
    var jsonScaleVals = "{$this->jsonScaleVals}";

    //Use menu for zoom selector
    var scaleSelector = new ScaleZoomMenu(menu_id, clickHandler, rangeType, scaleDispType, jsonScaleVals);

    // Called when zoom scale value selected, scale value passed
    function {$this->clickHandler}(scale) {
      if (!map.isDrawn) return;
      // Make currently selected tool
      panels.onToolClick('{$this->id}');
      var i, use_scale = null;
      var scaleMax = scaleSelector.getScaleMax();
      var scaleMin = scaleSelector.getScaleMin();
      var scaleArr = scaleSelector.getScaleArr();
      var current = parseInt(currentMapState.scale);

      switch(scale) {
      case "out":
        if (current == scaleMax) {
          error.print("Maximum map extent reached");
          return;
        }
        // default to the largest possible scale
        use_scale = scaleMax;
        // Find next largest scale in array, if it exists
        for (i=0;i<scaleArr.length;++i) {
          this_scale=scaleArr[i];
          if (this_scale>current) {
            use_scale=this_scale;
          } else {
            break;
          }
        }

        // if scale changed update the zoom scale menu
        if (use_scale > current) {
          // shift scale menu up one value
          scaleSelector.setZoomMenuVal("up");
        }

        break;
      case "in":
        if (current == scaleMin) {
          error.print("Minimum map extent reached");
          return;
        }

        // default to the smallest possible scale
        use_scale=scaleArr[scaleArr.length-1];
        // Find next smallest scale in array if it exists
        for (i=scaleArr.length-1;i>=0;--i) {
          this_scale=scaleArr[i];
          if (this_scale<current) {
            use_scale=this_scale;
          } else {
            break;
          }
        }
      
        // if scale changed update the drop-down menu
        if (use_scale < current) {
          // shift scale menu down one value
          scaleSelector.setZoomMenuVal("down");
        }
        break;
      default:
        // Otherwise a specific zoom value was passed, use it
        use_scale=scale;
    }
    map.updateMap("zoom[scale]="+use_scale,true);
  }\n
EOT;

    return $js;
  }

  
  public function onLoad() {
    $setup="";

    $onload=<<<EOT

      var toolObj=new Tool('{$this->id}','',false,false,'',''); 
      toolObj.tooltip="{$this->tooltip}";
      toolObj.canBeCurrent=false;
      panels.addTool(toolObj);\n
EOT;

    if ($this->isDefault) {
      $setup=<<<EOT
        panels.defaultTool="{$this->id}";
        updateTooltip('{$this->id}');\n
EOT;

    }
    return $onload.$setup;
  }


  public function afterMapLoad() {
    // Create Zoom bar, requires current map state data, thus done 
    // after map load

    $js=<<<EOT
    var scaleMin = scaleSelector.getScaleMin();
    var scaleMax = scaleSelector.getScaleMax();

    // Update scale menu if it hasn't been setup
    if (!scaleMin) {
        scaleSelector.updateScale();
    }

    // Update scale if min and max extents have changed (base map has changed)
    if (scaleMin != map.minScale || scaleMax != map.maxScale) {
        scaleSelector.updateScale();
    }

EOT;

    return $js;
  }


  public function asHTML() {
    $class=($this->isDefault)?"tool_on":"tool_off";
    $scale="";

    switch($this->scaleDispType) {
    case 'drop-down':    

      $scale .=<<<EOT
      <select id="{$this->menu_id}" alt="" title="">\n
      </select>\n
EOT;

      break; 
    }
    $mouse="onmouseover=\"updateTooltip('{$this->id}');\"";

    // Note: scale zoom menu loaded via afterMapLoad()
    $html=<<<EOT
      <tr><td class="$class" id="tool_{$this->id}" $mouse>
      <h4>Zoom To Scale:</h4><br />
      <img src="{$this->image}zoom_h_out.gif" alt="out scale" title="Zoom out to next scale" onclick="{$this->clickHandler}('out');"/>
      $scale
      <img src="{$this->image}zoom_h_in.gif" alt="in scale" title="Zoom in to next scale" onclick="{$this->clickHandler}('in');" />
      </td></tr>\n
EOT;

    return $html;
  }
} //End Class

?>
