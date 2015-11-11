<?php
 /***********************************************************************
 *
 * @file          common_functions.php
 *
 * $Id: common_functions.php 93 2006-12-21 22:16:29Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/common_functions.php $
 *
 * @project       Map-Fu
 *
 * This project was developed as part of the Oregon Sustainable
 * Community Digital Library (OSCDL) by Academic & Research Computing
 * at Portland State University with support by Oregon State
 * Library grants 245020, 245021.  Special thanks to Rose Jackson and 
 * the OSCDL project.
 *
 * @contributors  Tim Welch, Morgan Harvey
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

/***************************************************************************
 * map2img()
 * Converts point in decimal degree map coordinates to pixel coordinates
 *
 * width - of image
 * height - of image
 * point - array {x, y} point to convert in decimal degree coordinates
 * ext - {minx, maxx, miny, maxy} extent of map in decimal degree coordinates
 ***************************************************************************/
function map2img($width, $height, $point, $ext){
    $minx = $ext->minx;
    $miny = $ext->miny;
    $maxx = $ext->maxx;
    $maxy = $ext->maxy;

    $pt['minx'] = $minx;
    $pt['maxx'] = $maxx;
    $pt['miny'] = $miny;
    $pt['maxy'] = $maxy;

    if($point->x && $point->y){
        $x = $point->x;
        $y = $point->y;

        $pt['orig_x'] = $x;
        $pt['orig_y'] = $y;

        $range_x = abs($maxx-$minx);
        $range_y = abs($maxy-$miny);

        $pt['range_x'] = $range_x;
        $pt['range_y'] = $range_y;

        //percentage point is from top and left
        $perc_x = abs($x/$range_x);
        $perc_y = abs($y/$range_y);

        $pt['perc_x'] = $perc_x;
        $pt['perc_y'] = $perc_y;

        //total pixels top and left * percentage
        $x = $width * $perc_x;
        $y = $height * $perc_y;
    }

    $pt[0] = $pt['x'] = $x;
    $pt[1] = $pt['y'] = $y;
    return $pt;
}
?>