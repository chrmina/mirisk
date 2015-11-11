<?php
/***********************************************************************
 * @file          config.tmpl.php
 *
 * $Id: config.tmpl.php 107 2007-02-17 18:57:22Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/config.tmpl.php $
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

/***********************************************************************
 * This file is a template to be used for configuration of the
 * application. Do not alter this file, make a copy of it and alter it
 * to fit your needs.  See the SETUP file for details on how to do
 * this.
 ***********************************************************************/ 

/***********************************************************************
 * PATH CONFIG 
 ***********************************************************************/ 

//Define Map-Fu directory structure
$include_path="includes/";
$map_path="map/";
$image_path="images/";
$map_image_path=$map_path."images/";
$javascript_path=$include_path."Javascript/";
$php_path=$include_path."php/";
$abstract_path=$include_path."abstract/";
$tool_path=$include_path."Tools/";
$datasource_path=$include_path."DataSources/";
$banner_path=$include_path."Banners/";
$menu_path=$banner_path."Menus/";
$query_path=$include_path."Query/";
$cleanup_path=$include_path."cleanup/";
$help_path="help/";

/***********************************************************************
 * GENERAL CONFIG 
 ***********************************************************************/ 

/* app_name is used to create unique top-level function names,
 * preventing potential name collisions. For example mapfu_init()
 */
$app_name="mapfu";
$page_title="Map-Fu";
$main_stylesheet_configured = true;
$main_stylesheet = "main.css";

/***********************************************************************
 * TOOL CONFIG 
 ***********************************************************************/ 

/* the tools array is used to defines the tools to be made available
 * in the application and how they should be grouped.  Each key/value
 * pair represents the name of a tool group and the tools that belong
 * to that group.  each of the tools listed in a tool group should be
 * the name of a class that is defined in a file of the same name in
 * the directory defined by $tool_path.  For example: "ZoomIn" is the
 * name of a class defined in the file ZoomIn.php in the Tools
 * subdirectory.
 *
 * Note: there may optionally be one group that has no name and can be
 * used for tools that don't neatly fall into a related group.
 */
$tools=array(
  "Navigation"=>array("ZoomIn","ZoomOut","ZoomFull","ZoomTo", "Pan","ScaleZoom"),
  "Information"=>array("InfoQuery","PrintMap", "DownloadImage"),
  ""=>array("Reset")
);
/* default tool to select when Map-Fu loads */
$default_tool="ZoomIn";

/***********************************************************************
 * JAVASCRIPT CONFIG 
 ***********************************************************************/ 

$use_google_geocoder = false;

/****************************************************************************
 * The internal_js_scripts array defines all internal javascript files
 * to be loaded client-side.  These files should be located in
 * $javascript_path.  The extension for each file in the array is
 * expected to be .js.
 ***************************************************************************/
$internal_js_scripts=array('common_functions', 'json', 'ajax_utilities', 
  'queue', 'tabs', 'main', 'mapstate', 'panels','tool','layers','rubberBand', 
  'ref_rubber_band', 'debug', 'error', 'floater', 'scaleZoomMenu', 'menu');

if ($use_google_geocoder) {
    array_push($internal_js_scripts, 'geocoder_client');
    array_push($internal_js_scripts, 'google_geocoder');
}

/****************************************************************************
 * google_maps_url defines the location and key to use in loading the
 * google maps javascript code.  google maps is used for its geocoder.
 * To use it, you will need to acquire your own key at:
 * http://www.google.com/apis/maps/signup.html
 ***************************************************************************/
$google_maps_url = "http://maps.google.com/maps?file=api&amp;v=2&amp;key=YOUR KEY HERE";

/****************************************************************************
 * The external_js_scripts array defines all external third-party
 * javascript files to be loaded client-side.
 ***************************************************************************/

$external_js_scripts=array();
if ($use_google_geocoder)
    array_push($external_js_scripts, $google_maps_url);

//$external_js_scripts=array($google_maps_url);

/****************************************************************************
 * js_config is a string containing javascript configuration code
 * it is evaluated during setup making it available 
 ***************************************************************************/

/**** START JAVASCRIPT CODE ****/
$js_config = <<<EOT
// JS config, see config.php

/* Specific geocoder service to use.  May require loading an external
 * javascript file and/or providing a registration key
 */
var geocoder_service = "google_geocoder";

/* Define the type of results the google_geocoder should return
 *   getLatLng - return lat/lng of best match if there is one
 *   getLocations - return JSON structure giving detailed information about
 *     each match, allowing the best one to be selected by the user
 */
var google_geo_res_type = "getLocations";

/* define whether to update the map when a layer is turned on or off
 */
var update_map_on_layer_change = true;
EOT;
/**** END JAVASCRIPT CODE ****/

$update_map_on_layer_change = true;

/***********************************************************************
 * DATASOURCE CONFIG 
 ***********************************************************************/ 

/* $datasource specifies the PHP class to use to define the source to
 * load data from.  The class should be in a file of the same name and
 * be located in the directory defined by $datasource_path
 */
$datasource="SampleDatasource";
$mapfiles = array("US Map"=>"US_mapfile.map", "World Map"=>"World_mapfile.map");
$default_map = "US Map";


/***********************************************************************
 * BANNER CONFIG 
 ***********************************************************************/ 

/*(OPTIONAL) Defines the PHP class to use to create the banner at the
 *top of the page.  The class should be in a file of the same name and
 *be located in the directory defined by $banner_path
 */
$banner="DefaultBanner";  
$banner_height=-1; //If positive, specifies height of banner div
                   //container

/***********************************************************************
 * CLEANUP CONFIG 
 ***********************************************************************/ 

/* When use_cleanup is set to true the system looks for old temporary files and
 * removes them. It does this when a map session is started for the first time
 * (i.e. per user visit). The alternative to this would be to have an active
 * cron job that runs periodically to remove old temporary files. 
 */
$use_cleanup=true;

$include_temporary_mapfiles=false;

/* cleanup_threshold specifies the maximum age (in minutes) that a
 * temporary file can remain before being deleted by the cleanup
 * object. the default value of 1440 minutes equates to 24 hours.
 */
$cleanup_threshold=1440;

/***********************************************************************
 * LAYOUT CONFIG 
 ***********************************************************************/ 

$panels_width=300; //Defines width of left utility panel (layers,
                   //tools, datasource).  Left position of map div
                   //is based on this value
$panels_margin=6;  //Defines space to give in separating panel
                   //contents from the panel border
$layer_groups_open=true;

/***********************************************************************
 * MAP CONFIG 
 ***********************************************************************/ 

//minimum scale that map can be zoomed to. 1000 = 1:1000 = 1 inch to
//1000 inches
$min_map_scale=24000;

//Display units in degrees minutes seconds
$dms=false;

//Decimal digits of precision to display decimal degree values in
$dd_precision=6;

$fixed_coords = true;  //Display coords next to map? 
$floating_coords = false;  //Display floating coordinates check box on map?
$floating_coords_toggle = false; //Display floating coordinates on map
                                 //by default?
$resize_map_to_window=true;

/***********************************************************************
 * GEOCODER CONFIG
 ***********************************************************************/ 

/* define the layer to be used for displaying a geocoder result.
 * this layer must be defined in the current mapfile
 * see sample mapfiles for examples
 */
$geocode_result_layer = "GeocodeLayer";

/***********************************************************************
 * STATUS CONFIG
 ***********************************************************************/ 

ini_set("display_startup_errors","1");
ini_set("display_errors","1");
error_reporting(E_ALL);

/* error_on specifies whether the error tab should be created.  The
 * error tab is used by various components to display errors to the
 * user.  This should be set to true.
 */
$error_on=true;

/* debug_on specifies whether the debug tab should be created.  Useful
 * for javascript debugging.  Don't set to true on a live site unless
 * necessary.
 */
$debug_on=true;
?>
