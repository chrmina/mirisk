 /***********************************************************************
 * @file          geocoder_client.js
 *
 * $Id: geocoder_client.js 93 2006-12-21 22:16:29Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/geocoder_client.js $
 *
 * @project       Map-Fu
 *
 * This project was developed as part of the Oregon Sustainable
 * Community Digital Library (OSCDL) by Academic & Research Computing
 * at Portland State University with support by Oregon State
 * Library grants 245020, 245021.  Special thanks to Rose Jackson and 
 * the OSCDL project.
 *
 * @contributors  Tim Welch
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
 * GeocoderClient class
 *
 * This file contains the classes GeocoderClient, GeocoderResult and
 * GeocoderLocation. They are intended to provide a common interface
 * for geocoding services regardless of the provider.
 *
 * Configuration variables expected to be defined:
 *   geocoder_service - the name of the class
/***************************************************************************/
function GeocoderClient(){

  //stores callback function pointer passed by caller.  Results of a
  //geocode request are passed to this function
  this.callback = null;

  //Provide service name via config at some point
  this.geocoder_service = geocoder_service;
  this.geocoder = eval("new " + geocoder_service + '()');

  //Create local variable pointing to 'this' object
  var me = this;
  //process_start() is used as a dummy callback function which uses
  //"me" to put the correct object instance back in scope.
  this.process_start = function (result) {
    me.process(result);
  }
}

/****************************************************************************
 * geocode(function, string) - makes geocoding requests given an address.  
 *           The results are returned to the callback function provided
 * 
 * location - the location to geocode e.g. "123 45th St. Boston, MA 12345"
 * callback - the function to call, passing the geocoder results
 ***************************************************************************/
GeocoderClient.prototype.geocode = function (callback, address) {
  this.callback = callback;
  return this.geocoder.geocode(this.process_start, address);
}

/****************************************************************************
 * process(function, GeocoderResult) - Further processes the result of a
 *       geocode request.  Results are passed on using the callback function 
 *       provided in the original geocode request of the geocoding using the 
 *       callback function provided to the geocode
 *       function.
 * 
 * geoResult - GeocoderResult returned by geocoder service
 ***************************************************************************/
GeocoderClient.prototype.process = function (geoResult) {
  this.callback(geoResult);
}

/****************************************************************************
 * GeocoderResult class
 *
 * Generalized structure for storing a geocoder result.  Used by geocoder
 * service objects to provide results to the geocoder client in a common
 * format
/***************************************************************************/
function GeocoderResult(){
  this.address = null;  //Requested address
  this.success = false; //Geocoding succeeded or not
  this.status = false;  //Detailed result status
  this.statusStr = null; //Human understandeable statusStr
  this.locations = Array(); //Array of GeocoderLocation objects
}

GeocoderResult.prototype.getAddress = function (){
  return this.address;
}

GeocoderResult.prototype.setAddress = function (address){
  this.address = address;
}

GeocoderResult.prototype.getSuccess = function (){
  return this.success;
}

GeocoderResult.prototype.setSuccess = function (success){
  this.success = success;
}

GeocoderResult.prototype.getStatus = function (){
  return this.status;
}

GeocoderResult.prototype.setStatus = function (status){
  this.status = status;
}

GeocoderResult.prototype.getStatusStr = function (){
  return this.statusStr;
}

GeocoderResult.prototype.setStatusStr = function (statusStr){
  this.statusStr = statusStr;
}

GeocoderResult.prototype.addLocation = function (address, accuracy, accuracyStr, lat, lng, id) {
  var new_location = new GeocoderLocation(address, accuracy, accuracyStr, lat, lng, id);
  this.locations.push(new_location);
}

GeocoderResult.prototype.getLocation = function (num) {
  return this.locations[num];
}

GeocoderResult.prototype.toHTML = function (){
  return "<b>Geocode Request</b>:" + 
    "<br>Status: " + this.statusStr +
    "<br>Accuracy: " + this.accuracyStr + 
    "<br>Latitude: " + this.lat + 
    "<br>Longitude: " + this.lng;
}

/****************************************************************************
 * GeocoderLocation class
 *
 * Holds information for a single geocoder result including
 * location and textual information.  Created by the geocoder service
 * object and used by the geocode requester.
/***************************************************************************/
function GeocoderLocation(address, accuracy, accuracyStr, scale, lat, lng, id) {
  this.address = address;
  this.accuracy = accuracy;
  this.accuracyStr = accuracyStr;
  this.scale = scale;
  this.lat = lat;
  this.lng = lng;
  this.id = id;
}

GeocoderLocation.prototype.getAddress = function (){
  return this.address;
}

GeocoderLocation.prototype.getAccuracy = function (){
  return this.accuracy;
}

GeocoderLocation.prototype.getAccuracyStr = function (){
  return this.accuracyStr;
}

GeocoderLocation.prototype.getLat = function (){
  return this.lat;
}

GeocoderLocation.prototype.getLng = function (){
  return this.lng;
}

GeocoderLocation.prototype.getRadius = function (){
  return this.radius;
}

GeocoderLocation.prototype.getScale = function (){
  return this.scale;
}

GeocoderLocation.prototype.toHTML = function () {
  return "Address: " + this.address + 
    "<br>Accuracy: " + this.accuracyStr +
    "<br>Scale: " + addCommas(this.scale) +
    "<br>Lng: " + this.lng +
    "<br>Lat: " + this.lat;
}
