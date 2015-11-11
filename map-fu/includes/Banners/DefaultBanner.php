<?php
 /***********************************************************************
 * @file          DefaultBanner.php
 *
 * $Id: DefaultBanner.php 65 2006-11-15 01:08:33Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Banners/DefaultBanner.php $
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
require_once($abstract_path."Banner.php");
require_once($php_path."Collection.php");

class DefaultBanner extends Banner {
  
  function __construct() {
    //
  }
  
  function __destruct() {
    //
  }
  
  public function inlineCSS() {
    $css="#title {font-family: trebuchet, arial, sans-serif;}\n".
      "#title td {padding-left:10px; padding-right:10px; font-size:12px; display:inline;}\n".
      "#title_name {float:left; font-size:14px; font-weight:bold;}\n".
      "#about {float:right; padding-right:10px;}\n";
    return $css;
  }
  
  public function asHTML() {
    global $help_path;
    $about=$help_path."about.php";
    $year=date('Y');
    $html=<<<EOT
<table id="title" width="100%" cellpadding="2" cellspacing="8">
  <tr><td>
    <span id="about">Powered by <a href="javascript:;" onclick="openWin('http://www.arc.pdx.edu/map-fu-wiki','');">Map-Fu</a></span>
</table>\n
EOT;
    return $html;
  }
}

?>
