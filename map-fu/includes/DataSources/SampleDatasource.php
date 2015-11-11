<?php
 /***********************************************************************
 * @file          DefaultDataSource.php
 *
 * $Id: SampleDatasource.php 107 2007-02-17 18:57:22Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/DataSources/SampleDatasource.php $
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
 * This file contains the class DefaultDataSource. A datasource object is
 * intended to be used to provide and manage any datasource that the mapping
 * application may need -- specifically a list of available mapfiles and
 * the logic to allow the user to choose between them.
 * 
 * The data source should be the one to call a map draw initially, or on some
 * other event (like the user selecting a different data source).
 * 
 * For an explanation on any of the functions listed below, please see the
 * documentation on the base class, Component.    
 ***************************************************************************/
require_once($abstract_path."Component.php");

class SampleDatasource extends Component { 
  public $tabTitle = "Data Sources";
  public $defaultMap = "";
  public $currentMap = NULL;
  public $lastMap = NULL;
  public $showTab = true;
  public $mapfiles = array();
  function __construct() {
    global $mapfiles, $default_map;
    $this->defaultMap = $default_map;
    $this->mapfiles = $mapfiles;
    $this->currentMap = $this->defaultMap;
  }

  public function inlineCSS() {
    $css="#datasource {text-align:center;}\n";
    return $css;
  }
  
  public function inlineJavascript() {
    if ($this->defaultMap) {
      $js=<<<EOT
var legend_panel="";
var mapfile='{$this->mapfiles[$this->defaultMap]}';\n
EOT;
    } else {
      $js=<<<EOT
var mapfile=null;
EOT;
    }
    $js.=<<<EOT
function validateMapSelection() {
  var mapsel=document.getElementById("datasourceMapfiles");
  var new_mapfile=mapsel.options[mapsel.selectedIndex].value;
  if (new_mapfile!="" && new_mapfile!=mapfile) {
    mapfile=new_mapfile;
    infoTabs.removeAllTabs();
    objectInit();
    map.createMap();
  }
}\n

function drawLegend(legend_html) {

  var legend_id=map.getInfoTabId("Key");
  if (!legend_id) {
    legend_id=infoTabs.addTab("Key",80,240,240);
    map.assignedInfoTabNames.push("Key");
    map.assignedInfoTabIds.push(legend_id);

    legend_panel=document.getElementById(legend_id);
  }

  if (legend_id) {
    if (legend_panel) {
      try {
          legend_panel.innerHTML = legend_html;
	    } catch (e) {
	      error.print("Error creating reference map: "+e.message);
	    }
    }
  }
}\n
EOT;
    return $js;
  }
  
  public function onLoad() {
    // do not use this method to specify the map creation code -- setup.php
    // places that last in the application init javascript function
    $js=NULL;
    return $js;
  }


  public function afterMapLoad() {
    $js=<<<EOT
        if (contents.legend_html) {
            drawLegend(contents.legend_html);
        }
EOT;
    return $js;
  }

  public function asHTML() {
    $html=<<<EOT
<select id="datasourceMapfiles" onchange="validateMapSelection();">\n
EOT;
    if ($this->defaultMap) {
      $mapfile=$this->mapfiles[$this->defaultMap];
      $html.=<<<EOT
  <option value="$mapfile" selected="selected">{$this->defaultMap}</option>\n
EOT;
      foreach ($this->mapfiles as $mapname=>$mapfile) {
        if ($mapname!=$this->defaultMap) {
          $html.=<<<EOT
  <option value="$mapfile">$mapname</option>\n
EOT;
        }
      }
    } else {
      $html.=<<<EOT
  <option value="" selected="selected">--- Select A Map ---</option>\n
EOT;
      foreach ($this->mapfiles as $mapname=>$mapfile) {
        $html.=<<<EOT
  <option value="$mapfile">$mapname</option>\n
EOT;
      }
    }
    $html.=<<<EOT
</select>\n
EOT;
    return $html;
  }
}

?>
