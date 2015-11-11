<?php
 /***********************************************************************
 * @file          InfoQuery.php
 *
 * $Id: InfoQuery.php 63 2006-11-15 00:25:44Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/InfoQuery.php $
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
 * This file contains the class InfoQuery. It is intended to provide the look
 * and functionality of an interface tool that allows the user to select a
 * point or area on the map and perform a mapserver query on the active layers
 * at that point or area.
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/
require_once($abstract_path."Tool.php");

class InfoQuery extends Tool {
  private $queryProcessor;
  private $queryFormatter;
  
  function __construct(&$group) {
    global $image_path,$query_processor,$query_formatter;
    $this->name="Info Query";
    $this->id="info_query";
    $this->description="Use this tool to query for information about a feature on the map";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_query_1.png";
    $this->type=QUERY_LOCATION_TOOL;
    $this->clickHandler="InfoQueryClick";
    $this->selectionHandler="InfoQueryHandler";
    $this->lostFocusHandler="InfoQueryUnfocus";
    $this->queryProcessor=$query_processor;
    $this->queryFormatter=$query_formatter;
  }
  
  function __destruct() {
    //
  }
  
  public function inlineJavascript() {
    $js=<<<EOT
function {$this->clickHandler}() {
  panels.onToolClick('{$this->id}');
  registerEvent(glassdiv,'onmousedown',rubber.pointRubber);
}
function {$this->selectionHandler}() {
  var queryString=new Array();
  queryString.push(rubber.queryString("coords"));
  queryString.push("query_formatter="+escape('{$this->queryFormatter}'));
  var queryTabId;
  if (panels.currentTool.assignedInfoTabs.length==0) {
    //queryTabId=infoTabs.addTab('Query Results',140,300,300);
    queryTabId=infoTabs.addTab('Query Results',140,190,250);
    panels.currentTool.assignedInfoTabs.push(queryTabId);
  } else {
    queryTabId=panels.currentTool.assignedInfoTabs[0];
  }
  map.queryArea('{$this->queryProcessor}',queryTabId,queryString.join("&"));
  if (!infoTabs.tabIsOpen(queryTabId))
    infoTabs.tabClick(queryTabId);
}
function {$this->lostFocusHandler}() {
  unregisterEvent(glassdiv,'onmousedown',rubber.pointRubber);
}
function openQueryTab() {
  var queryTabId;
  var queryTool=panels.getTool('{$this->id}');
  if (queryTool && queryTool.assignedInfoTabs.length>0) {
    queryTabId=queryTool.assignedInfoTabs[0];
    if (!infoTabs.tabIsOpen(queryTabId))
      infoTabs.tabClick(queryTabId);
  }
}
function closeQueryTab() {
  var queryTabId;
  var queryTool=panels.getTool('{$this->id}');
  if (queryTool && queryTool.assignedInfoTabs.length>0) {
    queryTabId=queryTool.assignedInfoTabs[0];
    if (infoTabs.tabIsOpen(queryTabId))
      infoTabs.tabClick(queryTabId);
  }
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    $setup="";
    $onload=<<<EOT
  var toolObj=new Tool('{$this->id}','help',true,false,'{$this->selectionHandler}','{$this->lostFocusHandler}');
  toolObj.tooltip="{$this->tooltip}";
  panels.addTool(toolObj);\n
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
        <img src="{$this->image}" alt="{$this->name}" title="{$this->description}" onclick="{$this->clickHandler}();"/>
        <h4>{$this->name}</h4>
      </td></tr>\n
EOT;
    return $html;
  }
}

?>
