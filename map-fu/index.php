<?php
 /***********************************************************************
 * @file          index.php
 *
 * $Id: index.php 108 2007-02-25 02:08:59Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/index.php $
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
 *                David Percy, Tim Welch
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
ob_start();
require_once("config.php");
require_once("setup.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?php print $page_title; ?></title>

<?php
//CSS
print $inline_css_html;
print $include_css_html;

//Javascript includes
print $config_include_js_html;
print $internal_include_js_html;
print $external_include_js_html;
print $component_include_js_html;

//Inline javascript
print "<script type=\"text/javascript\" language=\"Javascript\">\n";
print $inline_js;
print $init_function;
print $uninit_function;
print $map_update_func;
print $global_js_vars;
print "</script>\n";
?>

</head>
<body id="body" <?php echo "$onload $onunload";?> >

<?php
/**************************** header *********************************/
$style=($banner_height>0)?"style=\"height:".$banner_height."px;\"":"";

print <<<EOT
  <div id="banner" $style>\n
EOT;

if (isset($oBanner) && $oBanner) 
  print $oBanner->asHTML();

print <<<EOT
  </div>\n
EOT;

/**************************** tabs ************************************/
$style="width:".$panels_width."px;";

print <<<EOT
  <div id="panels" style="$style">
  <ul>
  <li class="off" id="tools_li" onclick="panels.onPanelClick('tools')"><a class="off" id="tools_a">Tools</a></li>
  <li class="off" id="groups_li" onclick="panels.onPanelClick('groups')"><a class="off" id="groups_a">Layers</a></li>\n
EOT;

if ($oDataSource->showTab) {
  print <<<EOT
  <li class="off" id="source_li" onclick="panels.onPanelClick('source')">
  <a class="off" id="source_a">{$oDataSource->tabTitle}</a>
  </li>\n
EOT;
}

print <<<EOT
  </ul>
  </div>\n
EOT;

/*************************** groups/layers ****************************/
print <<<EOT
  <div id="groups_panel" style="$style">
    <div id="groupsbar" style="$style">
      <div id="groups"></div>
    </div>
  </div>\n
EOT;

/******************************** tools ***********************************/
print <<<EOT
  <div id="tools_panel" style="$style">
    <div id="toolbar" style="$style">
      <div id="tools">\n
EOT;

if (isset($tool_groups)) {
  $margin=6;
  $group_style="width:".($panels_width-$margin*2)."px;margin-left:".($margin-1)."px;";
  //print out toolgroups and their tools
  foreach ($tool_groups as $tool_group) {
    try {
      print $tool_group->asHTML($group_style);
    } catch (Exception $e) {
      print "Error rendering tool group HTML: ".$e->getMessage();
    }
  }
}

print <<<EOT
      </div>
      <div id="tooltips"></div>
    </div>
  </div>\n
EOT;

/******************************* datasource ******************************/
print <<<EOT
  <div id="source_panel" style="$style">
    <div id="sourcebar" style="$style">
      <div id="datasource">\n
EOT;

print $oDataSource->asHTML();
print <<<EOT
      </div>
    </div>
  </div>\n
EOT;

/****************************** panel common ******************************/
$chkd=($floating_coords_toggle)?"checked=\"checked\"":"";
$disp=($floating_coords)?"none":"block";

$fixed_coords_html = $floating_coords_html = "";
$update_buttom_html = "";

if ($fixed_coords) {
    $fixed_coords_html = <<<EOT
        <tr><td colspan="2">
          <table id="fixed_coords" style="display:$disp;">
            <tr><td>Latitude: <span id="current_lat"></span></td></tr>
            <tr><td>Longitude: <span id="current_lon"></span></td></tr>
          </table>
        </td></tr>
EOT;
}

if ($floating_coords) {
    $floating_coords_html = <<<EOT
        <tr>
          <td>Display Floating Coordinates:
          <input class="chk" type="checkbox" id="floating_coords" onclick="toggleFloatingCoordinates();" $chkd /></td>
        </tr>
EOT;
}

$coords_html = "<div id='coords_div'>" .
               $fixed_coords_html .
               $floating_coords_html .
               "<table></table></div>";

if (!$update_map_on_layer_change) {
    $update_button_html = <<<EOT
        <div id="update_div">
        <input class="button" type="button" id="update_button" value="Update Map" title="Apply changes to the map" onclick="$onupdateclick"/>
        </div>
EOT;
}

global $update_button_html;
print <<<EOT
  <div id="panels_common" style="$style">\n
  $update_button_html\n
  $coords_html\n
  </div>\n
EOT;

/********************** info tabs, menumap, rubberband ************************/
$style="left:".$panels_width."px;";
print <<<EOT
  <div id="map_tabs" style="$style"></div>
  <div id="map" style="z-index:1;top:67px; $style">
    <!-- onload - re-display the image after panning -->
    <img id="map_image" src="images/pixel.gif" width="1" height="1" onload='hideLoading(); map_image.style.display="block";'/>
  </div>
  <div id="glass"></div>
  <div id="alerts" style="width:300px; height:120px;"></div>
<img id="loading_img" src='./images/processing.gif'>
  <div id="rubberBand"></div>
  <div id="ref_rubberband"></div>
EOT;

/**************************** additional HTML ****************************/
if ($additional_html) print $additional_html;

print <<<EOT
  <div id="coords_panel" class="floating_panel" style="display:none; left:0px; top:0px; width:116px; height:28px; z-index:0;"></div>
  </body>
  </html>\n
EOT;

ob_end_flush();
?>
