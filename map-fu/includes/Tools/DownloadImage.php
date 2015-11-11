<?php
 /***********************************************************************
 * @file          DownloadImage.php
 *
 * $Id: DownloadImage.php 95 2006-12-22 17:08:27Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/DownloadImage.php $
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
 * This file contains the class DownloadImage. It is intended to allow
 * the user to customize and download an image of the current map or
 * legen for offline viewing or printing
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/

require_once($tool_path."tool_types.inc.php");
require_once($abstract_path."Tool.php");

class DownloadImage extends Tool {
  private $image_width;
  private $image_height;
  
  function __construct(&$group) {
    global $image_path;
    $this->name="Download Map Image";
    $this->id="print_tool";
    $this->description="Use this tool to create a printable version of the map or legend";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_print_1.png";
    $this->type=PRINT_TOOL;
    $this->clickHandler="PrintToolClick";
    $this->selectionHandler=NULL;
    $this->isDefault=false;
    $this->image_width=800;     // print image width
    $this->image_height=600;    // print image height
  }
  
  function __destruct() {
    //
  }
  
  public function inlineJavascript() {
    global $php_path;

    $js=<<<EOT
function {$this->clickHandler}() {
  panels.onToolClick('{$this->id}');
  map.setQueryString();
  var qs=map.queryString.split('&');
  window.open('{$php_path}image_download1.php?'+qs.join('&'),'mapPrint','menubar=yes,resizable=yes,scrollbars=yes,width=600,height=600');
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
  panels.addTool(toolObj);\n
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
