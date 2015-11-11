<?php
// BASED ON InfoQuery.php
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

class AddProject extends Tool {
  //private $queryProcessor;
  private $addprojProcessor;
  private $queryFormatter;
  
  function __construct(&$group) {
    //global $image_path,$query_processor,$query_formatter;
    global $image_path,$addproj_processor,$query_formatter;
    $this->name="Add Component";
    $this->id="add_project";
    $this->description="Use this tool to add a new component location on the map.";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_target.png";
    $this->type=QUERY_LOCATION_TOOL;
    $this->clickHandler="AddProjectClick";
    $this->selectionHandler="AddProjectHandler";
    $this->lostFocusHandler="AddProjectUnfocus";
    //$this->queryProcessor=$query_processor;
    $this->addprojProcessor=$addproj_processor;
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
    queryTabId=infoTabs.addTab('Add Component',140,190,270);
    panels.currentTool.assignedInfoTabs.push(queryTabId);
  } else {
    queryTabId=panels.currentTool.assignedInfoTabs[0];
  }
  map.queryArea('{$this->addprojProcessor}',queryTabId,queryString.join("&"));
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
      //var toolObj=new Tool('{$this->id}','help',true,false,'{$this->selectionHandler}','{$this->lostFocusHandler}');
  var toolObj=new Tool('{$this->id}','crosshair',true,false,'{$this->selectionHandler}','{$this->lostFocusHandler}');
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
