 /***********************************************************************
 *
 * @file          queue.js
 *
 * $Id: queue.js 84 2006-12-15 17:06:03Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/queue.js $
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
 * This file contains 2 objects: Queue and QueueItem. QueueItem holds the
 * state of a given AJAX request that has been queued to run synchronously with
 * other AJAX requests that may or may not have interdependencies (hence the
 * need to have them run synchronously). The Queue object manages the requests
 * and responses on behalf of each QueueItem object.
 ***************************************************************************/

/****************************************************************************
 * QueueItem object
 * 
 * holds the state of an AJAX request  
 ***************************************************************************/
function QueueItem(name,notifyFunction,url,useXML,timeout) {
  this.name=name;           // a name to identify the request
  this.state="pending";     // state of the request. possible values are: 
                            // pending, working and notifying
  this.notifyFunction=notifyFunction; // a function to call with the response.
                            // the notify function will always be passed 2
                            // arguments: status of the request and the response
                            // either a DOM object or text
  this.url=url;             // the url that will process the AJAX request. this
                            // includes the server-side file and any arguments
                            // encoded in a GET string.
  this.status="OK";         // status of the request. possible values are:
                            // OK, Timeout or an Error message
  this.processTimestamp=null; // a timestamp of when the request was sent (used
                            // in determining timeout)
  this.useXML=useXML;       // boolean specifying if the request expects an XML
                            // (DOM object) or plain text response
  this.http_request=null;   // the XMLHttpRequest object processing this request
  this.timeout=timeout;     // the time allowed for this request to process
                            // before aborting (in milliseconds)
  return this;
}

/****************************************************************************
 * Queue object
 * 
 * manages the activity of all queued requests.
 ***************************************************************************/ 
function Queue() {
  this.items=new Array();
  this.timeout=10000;   // default timeout of 10 seconds (in milliseconds)
                        // allowed for a queued item to run
  this.isIE=false;
  return this;
}

/****************************************************************************
 * init 
 * initializes the state of the Queue object
 ***************************************************************************/ 
Queue.prototype.init=function() {
  if (window.ActiveXObject) this.isIE=true;
  this.abortAll();
  this.items=new Array();
  // set the interval at which the queue should be checked for new requests
  // to process
  window.setInterval("queue.CheckQueue()",400);
}

/****************************************************************************
 * EnqueueItem 
 * appends a new request onto the list of waiting requests. an optional 5th
 * argument, if supplied will be assumed to be a timeout value. if not supplied,
 * the default will be used.  
 ***************************************************************************/ 
Queue.prototype.EnqueueItem=function(name,notifyFunction,url,useXML) {
  var timeout=(arguments.length==5)?arguments[4]:this.timeout;
  var queueItem=new QueueItem(name,notifyFunction,url,useXML,timeout);
  this.items.push(queueItem);
}

/****************************************************************************
 * CheckQueue 
 * checks the queue for waiting requests that should be aborted (due to 
 * timeout) or new requests that should be started. 
 ***************************************************************************/ 
Queue.prototype.CheckQueue=function() {
  if (queue.items.length>0) {
    var queueItem=queue.items[0];
    if (queueItem.state=="pending") {
      queue.ProcessRequest();
    } else if (queueItem.state=="working") {
      // check the timestamp to see how long its been working
      var now=new Date();
      if (now - queueItem.processTimestamp > queueItem.timeout) {
        // it's been running for longer than the timeout, so kill it
        queueItem.http_request.abort();
        // remove the item from the queue and notify the user
        queue.items.shift();
        if (queueItem.notifyFunction) {
          try {
            queueItem.notifyFunction('Timeout','');
          } catch (e) {
            error.print("Error notifying '"+queueItem.name+"': "+e.message);
            hideAlert();
          }
        } else {
          error.print("http request for '"+queueItem.name+"' timed out.");
        }
      }
    }
  }
}

/****************************************************************************
 * ProcessRequest 
 * creates an XMLHttpRequest object (or the IE equivalent) for a queued
 * request and sends the request off. 
 ***************************************************************************/ 
Queue.prototype.ProcessRequest=function() {
  var queueItem=queue.items[0];
  if (window.XMLHttpRequest) {
    try {
      queueItem.http_request=new XMLHttpRequest();
    } catch (e) {
      error.print("Error creating XMLHttpRequest object: "+e.message);
    }
  } else if (window.ActiveXObject) {
    try {
      queueItem.http_request=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        queueItem.http_request=new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e) {
        error.print("Error creating ActiveX http request object: "+e.message);
      }
    }
  }

  if (queueItem.http_request!=null) {
    queueItem.http_request.onreadystatechange=queue.RequestHandler;
    queueItem.http_request.open("GET",queueItem.url,true);
    queueItem.state="working";
    queueItem.processTimestamp=new Date();
    try {
      if (queue.isIE)
        queueItem.http_request.send();
      else
        queueItem.http_request.send(null);
    } catch (e) {
      queueItem.http_request=null;
      error.print("Error sending http request for '"+queueItem.name+"': "+e.message);
    }
  }
}

/****************************************************************************
 * RequestHandler 
 * handles a request's reponse when it has finished.
 ***************************************************************************/ 
Queue.prototype.RequestHandler=function() {
  var queueItem=queue.items[0];
  if (queueItem.http_request.readyState == 4) {
    queueItem.state="notifying";
    if (queueItem.http_request.status == 200) {
      queueItem.status="OK";
    } else {
      queueItem.status="Error. Status code: "+queueItem.http_request.status+"; message: "+queueItem.http_request.statusText;
    }
    if (queueItem.notifyFunction) {
      var tryNotification=true;
      var response="";
      if (queueItem.useXML) {
        if (queueItem.http_request.responseXML) {
          response=queueItem.http_request.responseXML;
        } else {
          error.print("Error notifying '"+queueItem.name+"': invalid responseXML.\n"+
            "Please check the files relating to the url for errors:\n"+queueItem.url);
          tryNotification=false;
        }
      } else {
        if (queueItem.http_request.responseText) {
          response=queueItem.http_request.responseText;
        } else {
          error.print("Error notifying '"+queueItem.name+"': invalid responseText.\n"+
            "Please check the files relating to the url for errors:\n"+queueItem.url);
          tryNotification=false;
        }
      }
      if (tryNotification) {
        try {
          queueItem.notifyFunction(queueItem.status,response);
        } catch (e) {
          error.print("Error notifying '"+queueItem.name+"': "+e.message);
          hideAlert();
        } 
      }
    }
    queueItem.http_request=null;
    queueItem=null;
    queue.items.shift();
  }
}

/****************************************************************************
 * abortAll 
 * aborts all pending AJAX requests. this should only be the first one in the
 * queue (because that's how a queue is supposed to work), but all are queued
 * requests checked for completeness.  
 ***************************************************************************/ 
Queue.prototype.abortAll=function() {
  for (var i=0;i<this.items.length;++i) {
    if (this.items[i].http_request)
      this.items[i].http_request.abort();
  }
}
