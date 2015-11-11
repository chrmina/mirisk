 /***********************************************************************
 * @file          ajax_utilities.js
 *
 * $Id: ajax_utilities.js 47 2006-11-07 00:44:15Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/ajax_utilities.js $
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

/***********************************************************************
 * retrieve text of an XML document element, including
 * elements using namespaces
 ***********************************************************************/ 
function getElementTextNS(prefix,local,parentElem,index) {
  var result=null;
  if (prefix && isIE) {
    // IE/Windows way of handling namespaces
    result=parentElem.getElementsByTagName(prefix+":"+local)[index];
  } else {
    // the namespace versions of this method
    // (getElementsByTagNameNS()) operate
    // differently in Safari and Mozilla, but both
    // return value with just local name, provided
    // there aren't conflicts with non-namespace element
    // names
    result=parentElem.getElementsByTagName(local)[index];
  }
  if (result) {
    // get text, accounting for possible
    // whitespace (carriage return) text nodes
    if (result.childNodes.length > 1) {
      return result.childNodes[1].nodeValue;
    } else if (result.firstChild) {
      return result.firstChild.nodeValue;
    }
  }
  return null;
}

function parseResponse(responseXML,errors_only) {
  var list=new Array(new Array,new Array);
  var id,value;
  if (errors_only)
    var content=responseXML.getElementsByTagName("error");
  else
    var content=responseXML.getElementsByTagName("content");
  if (content.length>0) {
    for (var i=0;i<content.length;++i) {
      id=getElementTextNS("", "id", content[i], 0);
      value=getElementTextNS("", "value", content[i], 0);
      if (id && value) {
        if (typeof id == 'object' && id.constructor == Array) {
          for (j=0;j<id.length;++j) {
            list[0].push(id[j]);
            list[1].push(value[j]);
          }
        } else {
          list[0].push(id);
          list[1].push(value);
        }
      }
		}
		if (list[0].length>0) {
		  return list;
		} else {
		  return false;
		}
  } else {
    return false;
  }
}

function getParsedItemById(parsedItems,id) {
  for (var i=0;i<parsedItems[0].length;++i) {
    if (parsedItems[0][i]==id) {  // is this the id sought?
      return parsedItems[1][i];   // return the value for this id
    }
  }
  return null;
}

function dumpContents(parsedItems) {
  for (var i=0;i<parsedItems[0].length;++i) {
    debug.print(i+": "+parsedItems[0][i]+"="+parsedItems[1][i]);
  }
}