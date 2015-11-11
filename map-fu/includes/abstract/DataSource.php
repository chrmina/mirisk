<?php
 /***********************************************************************
 * @file          DataSource.php
 *
 * $Id: DataSource.php 64 2006-11-15 00:52:40Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/abstract/DataSource.php $
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

abstract class DataSource {
  /**************************************************************************
    inlineCSS:
    Should be used to return CSS style information that gets included in the 
    HTML document's head section. Any derived class that needs inline css 
    must override this function.
  **************************************************************************/
  public function inlineCSS() { return NULL; }

  /**************************************************************************
    includeCSS:
    Should be used to return the path of a css file that will be used 
    in the HTML document created. Any derived class that needs a css 
    file must override this function.
  **************************************************************************/
  public function includeCSS() { return NULL; }
  
  /**************************************************************************
    inlineJavascript:
    Should be used to return Javascript code (like a function or inline code) 
    that gets included in the HTML document's head section. Any derived class 
    that needs inline javascript must override this function.
  **************************************************************************/
  public function inlineJavascript() { return NULL; }

  /**************************************************************************
    includeJavascript:
    Should be used to return the path of a Javascript file that will be used 
    in the HTML document created. Any derived class that needs a javascript 
    file must override this function.
  **************************************************************************/
  public function includeJavascript() { return NULL; }

  /**************************************************************************
    onLoad:
    Should be used to return Javascript code that gets executed in the HTML 
    document body's onload event. Any derived class that needs onload 
    initializing code must override this function.
  **************************************************************************/
  public function onLoad() { return NULL; }
  
  /**************************************************************************
    onUnload:
    Should be used to return Javascript code that gets executed in the HTML 
    document body's onunload event. Any derived class that needs onunload 
    cleanup code must override this function.
  **************************************************************************/
  public function onUnload() { return NULL; }
  
  /**************************************************************************
    beforeMapUpdate:
    Should be used to return Javascript code that gets executed when the update
    button is clicked (or a map update event occurs) but before the map is 
    updated. Any derived class that needs to do something when the map gets 
    updated must override this function.
  **************************************************************************/
  public function beforeMapUpdate() { return NULL; }
  
  /**************************************************************************
    afterMapUpdate:
    Should be used to return Javascript code that gets executed when the update
    button is clicked (or a map update event occurs) but after the map is 
    updated. Any derived class that needs to do something when the map gets 
    updated must override this function.
  **************************************************************************/
  public function afterMapUpdate() { return NULL; }
  
  /**************************************************************************
    asHTML:
    Must be over-ridden by any derived objects. Should be used to return
    well-formed XHTML that will draw the layer on a web page.
  **************************************************************************/
  abstract public function asHTML();
}

?>
