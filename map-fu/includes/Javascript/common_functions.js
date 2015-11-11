 /***********************************************************************
 * @file          common_functions.php
 *
 * $Id: common_functions.js 84 2006-12-15 17:06:03Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/Javascript/common_functions.js $
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

/***************************************************************************
 * This file contains useful common functions
 ***************************************************************************/

/***************************************************************************
* intRound() 
* Round the given number keeping the given number of significant digits
***************************************************************************/
function intRound(num, sig) {
    var numStr = num + '';
    var numLen = numStr.length;
    var numToRound = numLen - sig;
    if (numToRound > 0) {
        for(var j=0;j<numToRound;++j) {num = num/10;}
        num = parseInt(num);
        for(var k=0;k<numToRound;++k) {num = num*10;}
        return num;
    } else {
        return num;
    }
}

function decRound(num, sig) {
  var consts = new Array(1, 10, 100, 1000, 10000, 10000, 100000);
  return Math.round(num * consts[sig]) / consts[sig];
}


/***************************************************************************
 * addCommas()
 * Format the given number, adding commas, and return as a string
 ***************************************************************************/
function addCommas(num)
{
    var str = num + '';
    x = str.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var regex = /(\d+)(\d{3})/;
    while (regex.test(x1)) {

        x1 = x1.replace(regex, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

/***************************************************************************
 * arrToStr()
 * Converts array to human readable form
 ***************************************************************************/
function arrToStr(array) {
    var result = '';
    for (var i=0; i<array.length; i++) {
        result += "["+i+"] is " + array[i] + "<br>";
    }
    return result;
}