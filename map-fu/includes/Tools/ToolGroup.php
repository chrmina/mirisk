<?php
 /***********************************************************************
 * @file          ToolGroup.php
 *
 * $Id: ToolGroup.php 62 2006-11-14 22:10:14Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Tools/ToolGroup.php $
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
 * This file contains the class ToolGroup, used in holding and maintaining a
 * group of Tool related objects.    
 ***************************************************************************/

require_once($php_path.'Collection.php');

class ToolGroup {
  public $name;
  private $tools;
  
  function __construct($name=NULL) {
    $this->name=$name;
    $this->tools=array();
  }
  
  function __destruct() {
    $this->tools=array();
  }
  
  public function AddTool(&$tool) {
    $this->tools[$tool->name]=$tool;
  }
  
  public function RemoveTool($tool_name) {
    if (array_key_exists($tool_name,$this->tools))
      unset($this->tools[$tool_name]);
  }
  
  public function &GetTool($tool_name) {
    if (array_key_exists($tool_name,$this->tools))
      return $this->tools[$tool_name];
    else
      return NULL;
  }
  
  public function ToolNames() {
    return array_keys($this->tools);
  }
  
  public function ToolIDs() {
    $ids=array();
    foreach ($this->tools as $name=>$tool)
      $ids[]=$tool->id;
    return $ids;
  }
  
  public function asHTML($style="") {
    $html=<<<EOT
      <div class="toolgroup" style="$style">\n
EOT;
    if ($this->name) {
      $html.=<<<EOT
        <h4>{$this->name}</h4>\n
EOT;
    }
    $html.=<<<EOT
        <table class="toolgroup_tools" cellspacing="0">\n
EOT;
    foreach ($this->tools as $name=>$tool)
      $html.=$tool->asHTML();
    $html.=<<<EOT
        </table>
      </div>\n
EOT;
    return $html;
  }
  
  public function ToolClickHandlers() {
    $handlers=array();
    foreach ($this->tools as $name=>$tool)
      $handlers[]=$tool->clickHandler;
    return $handlers;
  }
  
  public function Count() {
    return $this->tools->Count();
  }
}

?>