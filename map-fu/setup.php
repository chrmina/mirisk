<?php
 /***********************************************************************
 *
 * @file          setup.php
 *
 * $Id: setup.php 106 2007-02-09 20:56:32Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/setup.php $
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
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 ***********************************************************************/ 

/****************************************************************************
 * This file does setup of the application including:
 * 
 * - Generating javascript includes 
 * - Creating PHP component and tool objects 
 * - Querying component and tool objects for the CSS, javascript, and
 *   HTML needed to create, configure and use them client-side.  These
 *   inclusions are inserted into various parts of the code by other files
 * - Finally, boilerplate JS functions are generated to do the major
 *   setup and teardown of the application client-side.
 ***************************************************************************/

//Will contain HTML script tags which load JS code (inline or local
//file). Inserted into the document head.
$config_include_js_html = "";
$internal_include_js_html = "";
$external_include_js_html = "";

//Will contain CSS, javascript, and PHP extracted from components and
//tools. Inserted inline into the document head.
$inline_css=$include_css=NULL;  //Used in setup.php
$inline_js=$include_js=NULL;    //Used in setup.php
$onload_js=$onunload_js=NULL;   //Used in setup.php
$before_map_update_js=$after_map_update_js=NULL;  //Used in setup.php
$after_map_load_js=NULL;        //Used in MapJS.php
$additional_html=NULL;          //Used in index.php

//Will contain HTML to load the CSS and JS stored in some of the variables above
$include_css_html = "";
$inline_css_html = "";
$component_include_js_html = "";

//Will contain top-level map-fu JS functions to be inserted inline
$init_func = "";
$uninit_func = "";
$map_update_func = "";

//Will contain JS function calls to the functions loaded into the variables above
$onload = "";
$onunload = "";
$onupdateclick = "";


// Insert map-fu config JS code into script tag
$config_include_js_html = "<script type='text/javascript' language='Javascript'>" .
      $js_config . 
      "</script>\n";

// Create script tags to load each internal map-fu javascript file
foreach ($internal_js_scripts as $js_script) {
  $src=$javascript_path.$js_script.".js";
	$internal_include_js_html .= "<script type='text/javascript' language='Javascript' src='$src'></script>\n";
}

// Create script tags to load each external javascript file using the provided URL
foreach ($external_js_scripts as $js_url) {
    $external_include_js_html .= "<script type='text/javascript' language='Javascript' src='$js_url'></script>\n";
}

//Setup configured data source
if (isset($datasource) && strlen($datasource)>0) {
  require_once($datasource_path.$datasource.".php");
  eval("\$oDataSource=&new $datasource();");
  if (isset($oDataSource) && $oDataSource) {
    compileInclusions($oDataSource);
  }
} else {
  throw new Exception("Invalid datasource");
}

//Setup configured tools
require_once($tool_path."ToolGroup.php");
$tool_groups=array();

//for each tool group defined in tools array
foreach ($tools as $group_name=>$group_tools) {
  if ($group_name && strlen($group_name)>0)
    $toolgroup=&new ToolGroup($group_name);
  else
    $toolgroup=&new ToolGroup();
  foreach ($group_tools as $tool_name) {
    require_once($tool_path.$tool_name.".php");
    eval("\$t=&new $tool_name(\$toolgroup);");
    if (isset($t) && $t) {
      $t->isDefault=($tool_name==$default_tool);
      try {
        compileInclusions($t);
        $toolgroup->AddTool($t);
      } catch (Exception $e) {
        print "Error adding tool to tool group: ".$e->getMessage();
      }
    }
  }
  $tool_groups[]=$toolgroup;
}

//Setup banner
if (isset($banner) && strlen($banner)>0) {
  require_once($banner_path.$banner.".php");
  eval("\$oBanner=&new $banner();");
  if (isset($oBanner) && $oBanner) {
    compileInclusions($oBanner);
  }
}

//Setup client-side map functionality
require_once($php_path."MapJS.php");
$mapJS=&new MapJS();
compileInclusions($mapJS);

// manage session information
$session_id=uniqid(rand(),true);
session_name($app_name);
session_set_cookie_params(0);
session_start();
$_SESSION['id']=$session_id;

// remove any old temporary files
if ($use_cleanup) {
  require_once($cleanup_path."Cleaner.php");
  $oCleaner=&new Cleaner();
  $oCleaner->removeTemporaryFiles();
}

//Add the main stylesheet filename to the include_css array
if ($main_stylesheet_configured) {
    if ($main_stylesheet) {
        if (!$include_css) {
            $include_css = array();
        }
        array_push($include_css, $main_stylesheet);
    } else {
        die("Main stylesheet not configured. Please define or turn off in config.php");
    }
}
    

// include css files provided by components
if ($include_css) {
  foreach ($include_css as $path) {
    $include_css_html .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$path\"/>\n";
  }
}

// include inline css provided by components
if ($inline_css) {
  $inline_css_html .= "<style type=\"text/css\">\n".
      $inline_css.
      "</style>\n";
}

// include javascript files provided by components
if ($include_js) {
  foreach ($include_js as $path) {
    $component_include_js_html .= "<script type=\"text/javascript\" language=\"Javascript\" src=\"$path\"></script>\n";
  }
}


// Create init function
$create_map_call = $oDataSource->currentMap ? "  map.createMap();" : "";
$init_function_name=$app_name."_init";
$init_function = <<<EOT
function $init_function_name() {
  main_init();
  $onload_js
  $create_map_call
}

EOT;

// Create un-init function
$uninit_function_name=$app_name."_cleanup";
$uninit_function = <<<EOT
function $uninit_function_name() {
  $onunload_js
}

EOT;

// Create the map update button handler
$update_function_name=$app_name."_update";
$map_update_func = <<<EOT
function $update_function_name() {
  $before_map_update_js
  map.updateMap(null,true);
  $after_map_update_js
}

EOT;

/**********************************************************************/

//Set onload call, to be inserted into body tag in index.php
$onload="onload=\"".$init_function_name."();\"";

//Set onunload call, to be inserted into body tag in index.php
$onunload="onunload=\"".$uninit_function_name."();\"";

//Set map update call.
$onupdateclick=$update_function_name."();";

/**********************************************************************/

$global_js_vars = <<<EOT
var dataSource='$datasource';
var session_id='$session_id';
var panels_width=$panels_width;
var panels_margin=$panels_margin;

EOT;
$global_js_vars .= ($debug_on)?"var debug_on=true;\n":"var debug_on=false;\n";
$global_js_vars .= ($error_on)?"var error_on=true;\n":"var error_on=false;\n";
$global_js_vars .= ($floating_coords_toggle)?"var floating_coords_toggle=true;\n":"var floating_coords_toggle=false;\n";
$global_js_vars .= ($floating_coords)?"var floating_coords=true;\n":"var floating_coords=false;\n";
$global_js_vars .= ($fixed_coords)?"var fixed_coords=true;\n":"var fixed_coords=false;\n";
$global_js_vars .= ($layer_groups_open)?"var layer_groups_open=true;\n":"var layer_groups_open=false;\n";
$global_js_vars .= ($resize_map_to_window)?"var resize_map_to_window=true;\n":"var resize_map_to_window=false;\n";

//////////



/*****************************************************************************
 * compileInclusions()
 * this function takes a Component object and inspects it for css, javascript
 * and additional HTML that it may need for proper functionality. if found, it
 * adds these values to a global list of "things" to be included in index.php.   
 ****************************************************************************/ 
function compileInclusions(&$obj) {
  global $inline_css,$include_css,$inline_js,$include_js,$onload_js;
  global $onunload_js,$before_map_update_js,$after_map_update_js,$additional_html;
  global $after_map_load_js;
  
  // css style information
  if (($css=$obj->inlineCSS())!=NULL) {
    if ($inline_css==NULL)
      $inline_css="";  // initalize for future concatenations
    $inline_css.=$css;
  }
  // includeCSS will be a path or an array of paths to a css file(s)
  if (($css=$obj->includeCSS())!=NULL) {
    if ($include_css==NULL)
      $include_css=array();  // initalize for future additions
    // only add those paths that haven't been added before
    if (is_array($css)) {
      foreach ($css as $path) {
        if (!in_array($path,$include_css))
          $include_css[]=$path;
      }
    } else {
      if (!in_array($css,$include_css))
        $include_css[]=$css;
    }
  }

  // Javascript to be executed after each map load
  if (($js=$obj->afterMapLoad())!=NULL) {
    if ($after_map_load_js==NULL)
      $after_map_load_js="";  // initalize for future concatenations
    $after_map_load_js.=$js;
  }
  
  // general javascript functionality
  if (($js=$obj->inlineJavascript())!=NULL) {
    if ($inline_js==NULL)
      $inline_js="";  // initalize for future concatenations
    $inline_js.=$js;
  }
  // includeJavascript will be a path or an array of paths to a javascript file(s)
  if (($js=$obj->includeJavascript())!=NULL) {
    if ($include_js==NULL)
      $include_js=array();  // initalize for future additions
    // only add those paths that haven't been added before
    if (is_array($js)) {
      foreach ($js as $path) {
        if (!in_array($path,$include_js))
          $include_js[]=$path;
      }
    } else {
      if (!in_array($js,$include_js))
        $include_js[]=$js;
    }
  }

  // body load functionality
  if (($js=$obj->onLoad())!=NULL) {
    if ($onload_js==NULL)
      $onload_js="";  // initalize for future concatenations
    $onload_js.=$js;
  }

  // body unload functionality
  if (($js=$obj->onUnload())!=NULL) {
    if ($onunload_js==NULL)
      $onunload_js="";  // initalize for future concatenations
    $onunload_js.=$js;
  }
  
  // before map update functionality
  if (($js=$obj->beforeMapUpdate())!=NULL) {
    if ($before_map_update_js==NULL)
      $before_map_update_js="";  // initalize for future concatenations
    $before_map_update_js.=$js;
  }

  // after map update functionality
  if (($js=$obj->afterMapUpdate())!=NULL) {
    if ($after_map_update_js==NULL)
      $after_map_update_js="";  // initalize for future concatenations
    $after_map_update_js.=$js;
  }
  
  // any additional HTML that a component may need
  if (($html=$obj->additionalHTML())!=NULL) {
    if ($additional_html==NULL)
      $additional_html="";  // initalize for future concatenations
    $additional_html.=$html;
  }
}
?>
