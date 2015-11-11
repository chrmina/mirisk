<?php
 /***********************************************************************
 * @file          ZoomFull.php
 *
 * $Id: ZoomFull.php 98 2007-01-02 00:21:32Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/ZoomFull.php $
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
 * This file contains the class ZoomFull. It provides a tool which
 * allows the user to zoom out to the maximum map extent.  Layer state
 * stays the same.  This is different from a map reset.
 ***************************************************************************/


require_once($tool_path."tool_types.inc.php");
require_once($abstract_path."Tool.php");

class ZoomFull extends Tool {
  
  function __construct(&$group) {
    global $image_path;
    $this->name="Zoom Full";
    $this->id="zoom_full_tool";
    $this->description="Zoom out to the full map extent.";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_zoomout_1.png";
    $this->type=CHANGE_EXTENT_TOOL;
    $this->clickHandler="ZoomFullClick";
    $this->selectionHandler=NULL;
    $this->isDefault=false;
  }
  
  function __destruct() {
    //
  }
  
  public function inlineJavascript() {
    $full_path="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
    $js=<<<EOT
function {$this->clickHandler}() {
  map.updateMap("zoom[scale]="+map.maxScale,true);
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    $setup="";
    $onload=<<<EOT
  var toolObj=new Tool('{$this->id}');
  toolObj.cursorStyle="default";
  toolObj.tooltip="{$this->tooltip}";
  toolObj.canBeCurrent=false;
  toolObj.usesRubberBand=false;
  panels.tools.push(toolObj);\n
EOT;
    if ($this->isDefault) {
      $setup=<<<EOT
  panels.currentTool="{$this->id}";
  panels.currentToolObj=toolObj;
  {$this->clickHandler}();
  updateTooltip('{$this->id}');\n
EOT;
    }
    return $onload.$setup;
  }

  public function asHTML() {
    $class=($this->isDefault)?"tool_on":"tool_off";
    $html=<<<EOT
      <tr><td class="$class" id="tool_{$this->id}" onclick="{$this->clickHandler}();" onmouseover="updateTooltip('{$this->id}');">
        <img src="{$this->image}" alt="{$this->name}" title="{$this->description}" onclick="{$this->clickHandler}();"/>
        <h4>{$this->name}</h4>
      </td></tr>\n
EOT;
    return $html;
  }
}

?>
