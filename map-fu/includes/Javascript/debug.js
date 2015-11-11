 /***********************************************************************
 * @file          debug.js
 *
 * $Id: debug.js 84 2006-12-15 17:06:03Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/debug.js $
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

/****************************************************************************
 * This file contains an object that prints output to an info tab.
 ***************************************************************************/

function Debug() {
  this.debug_id=null;
  this.debug_panel=null;
  return this;
}

/***************************************************************************
* init()
* creates an info tab as a place to print output
***************************************************************************/
Debug.prototype.init=function() {
  //If a tab has not already been created
  if (!this.debug_id) {
    //Create the tab
    this.debug_id=infoTabs.addTab("Debug",80,260,250);
    //If successfully created, set it up
    if (this.debug_id) {
      this.debug_panel=document.getElementById(this.debug_id);
      this.clear();
    }
  }
}

/***************************************************************************
* print()
* prints a message to the output info tab
***************************************************************************/
Debug.prototype.print=function(message) {
  if (this.debug_panel) {
    this.debug_panel.innerHTML+="<br/>"+message;
  }
}

/***************************************************************************
* clear()
* removes all output from the output info tab
***************************************************************************/
Debug.prototype.clear=function() {
  if (this.debug_panel) {
    this.debug_panel.innerHTML="<a href=\"javascript:;\" onclick=\"debug.clear();\">Clear</a>";
  }
}
