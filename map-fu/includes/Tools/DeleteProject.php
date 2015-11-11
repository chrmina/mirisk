<?php
 // BASED ON PrintMap.php
/****************************************************************************
 * This file contains the PrintMap class.  It defines a tool which
 * allows the map image to be printed by loading it into a new browser
 * window by itself.
 * 
 * For an explanation on any of the properties and methods listed below, please
 * see the documentation on the base classes, Tool and Component.    
 ***************************************************************************/
require_once("tool_types.inc.php");
require_once($abstract_path."Tool.php");

class DeleteProject extends Tool {
  private $delprojProcessor;
  
  function __construct(&$group) {
    global $image_path,$delproj_processor;
    $this->name="Delete Component";
    $this->id="delete_project";
    $this->description="Delete a component from the map and the database using the component's ID.";
    $this->tooltip="<span>".$this->name."</span><br/>".$this->description;
    $this->group=$group;
    $this->image=$image_path."button_delproj.png";
    $this->type=DELETE_PROJECT_TOOL;
    $this->clickHandler="DeleteProjectClick";
    $this->selectionHandler=NULL;
    $this->isDefault=false;
    $this->delprojProcessor=$delproj_processor;
  }
  
  function __destruct() {
    //
  }
  
  public function inlineJavascript() {
    $js=<<<EOT
function {$this->clickHandler}() {
  panels.onToolClick('{$this->id}');
  window.open('{$this->delprojProcessor}','result','width=400,height=180,menubar=no,resizable=no,scrollbars=yes');
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    $setup="";
    $onload=<<<EOT
      var toolObj=new Tool('{$this->id}');
      toolObj.cursorStyle="default";
      toolObj.canBeCurrent=false;
      toolObj.usesRubberBand=false;
      panels.addTool(toolObj);\n
      toolObj.tooltip="{$this->tooltip}";
EOT;
    if ($this->isDefault) {
      $setup=<<<EOT
        panels.currentTool="{$this->id}";
        panels.currentToolObj=toolObj;
        {$this->clickHandler}();\n
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
