<?php
 /***********************************************************************
 * @file          MapSize.php
 *
 * $Id: MapSize.php 47 2006-11-07 00:44:15Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/MapSize.php $
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
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/
require_once($abstract_path."Tool.php");

class MapSize extends Tool {
  
  function __construct(&$group) {
    global $image_path,$resize_map_to_window;
    $this->name="Map Size";
    $this->id="map_size";
    $this->description="Use this tool to set the size of the map";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_mapsize.png";
    $this->type=CHANGE_SIZE_TOOL;
    $this->clickHandler="MapSizeClick";
    $this->selectionHandler="MapSizeHandler";
    $this->lostFocusHandler="MapSizeUnfocus";
    $this->sizes=array("Small"=>"400 x 300","Medium"=>"640 x 480","Large"=>"800 x 600");
    $this->default_size="Medium";
    $resize_map_to_window=false;
  }
  
  function __destruct() {
    //
  }
  
  public function inlineCSS() {
    $css="#".$this->id."_panel {background-color:#fff;}\n";
    return $css;
  }
  
  public function inlineJavascript() {
    $js=<<<EOT
var mapSizeLeft=mapSizeTop=INVALID;
var map_width=map_height=INVALID;
var mapSizeFloater;
function {$this->clickHandler}(evt) {
  panels.onToolClick('{$this->id}');
  if (mapSizeLeft==INVALID && mapSizeTop==INVALID) positionMapSizePanel();
  mapSizeFloater.show();
}
function {$this->selectionHandler}() {
  var newSize,mapsize,temp;
  var wid=INVALID,hgt=INVALID;
  for (var i=0;(mapsize=document.getElementById("mapsize_"+i))!=null;++i) {
    if (mapsize.checked) {
      temp=mapsize.value.split("x");
      wid=parseInt(temp[0]);
      hgt=parseInt(temp[1]);
    }
  }
  mapSizeFloater.hide();
  if (!isNaN(wid) && !isNaN(hgt) && wid!=INVALID && hgt!=INVALID) { 
    if (wid!=map_width || hgt!=map_height) {
      map_width=wid;
      map_height=hgt;
      resize(map_width,map_height);
    }
  }
}
function {$this->lostFocusHandler}() {
  mapSizeFloater.hide();
}
function positionMapSizePanel() {
  var map_size_panel=document.getElementById("{$this->id}_panel");
  var map_size_tool=document.getElementById("tool_{$this->id}");
  mapSizeLeft=findPosX(map_size_tool)+6;
  if (map_size_tool.offsetWidth) mapSizeLeft+=map_size_tool.offsetWidth;
  mapSizeTop=findPosY(map_size_tool);
  mapSizeFloater.move(mapSizeLeft,mapSizeTop,0,0);
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    $setup="";
    $spec=explode("x",$this->sizes[$this->default_size]);
    $wid=trim($spec[0]);
    $hgt=trim($spec[1]);
    $onload=<<<EOT
  var toolObj=new Tool('{$this->id}','default',false,false,'{$this->selectionHandler}','{$this->lostFocusHandler}');
  toolObj.tooltip="{$this->tooltip}";
  panels.addTool(toolObj);
  mapSizeFloater=new Floater("{$this->id}_panel");
  floaters.addFloater(mapSizeFloater);
  map_width=$wid;
  map_height=$hgt;
  resize(map_width,map_height);\n
EOT;
    if ($this->isDefault) {
      $setup=<<<EOT
  panels.defaultTool="{$this->id}";
  {$this->clickHandler}();
  updateTooltip('{$this->id}');\n
EOT;
    }
    return $onload.$setup;
  }

  public function asHTML() {
    $class=($this->isDefault)?"tool_on":"tool_off";
    $mouse="onmouseover=\"updateTooltip('{$this->id}');\"";
    $html=<<<EOT
      <tr><td class="$class" id="tool_{$this->id}" onclick="{$this->clickHandler}();" $mouse>
        <img src="{$this->image}" alt="{$this->name}" title="{$this->description}" onclick="{$this->clickHandler}();" />
        <h4>{$this->name}</h4>
      </td></tr>\n
EOT;
    return $html;
  }
  
  public function additionalHTML() {
    global $image_path;
    $style="position:absolute; display:none; left:0px; top:0px; width:180px; height:140px; z-index:0;";
    $html=<<<EOT
<div id="{$this->id}_panel" class="floating_panel" style="$style">
  <table cellspacing="0">
    <tr>
      <th><img src="{$image_path}/close.png" onclick="mapSizeFloater.hide();" width="16" height="16" alt="Close" title="Close" /></th>
      <th>Set Map Size</th>
    </tr>
    <tr><td colspan="2">
      <table width="100%">\n
EOT;
    $i=0;
    foreach ($this->sizes as $size=>$desc) {
      $id="mapsize_".$i;
      $chkd=($size==$this->default_size)?"checked=\"checked\"":"";
      $html.=<<<EOT
        <tr>
          <td><input type="radio" name="mapsize" id="$id" value="$desc" $chkd /></td>
          <td>$size ($desc)</td>
        </tr>\n
EOT;
      ++$i;
    }
    $html.=<<<EOT
        <tr>
          <td align="center" colspan="2"><input class="button" type="button" value="Set Size" onclick="{$this->selectionHandler}();" /></td>
        </tr>
      </table>
    </td></tr>
  </table>
</div>\n
EOT;
    return $html;
  }
}

?>
