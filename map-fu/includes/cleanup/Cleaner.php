<?php
 /***********************************************************************
 * @file          Cleaner.php
 *
 * $Id: Cleaner.php 64 2006-11-15 00:52:40Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/cleanup/Cleaner.php $
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
class Cleaner {
  public $err=NULL;
  
  public function removeTemporaryFiles($force=false) {
    global $map_path,$map_image_path,$cleanup_threshold,$include_temporary_mapfiles;
    if ($force)
      $threshold=0;
    else
      $threshold=$cleanup_threshold;
    // remove temporary map images (map, legend icon, scalebar, etc.)
    try {
      $this->remove($map_image_path,$threshold);
    } catch (Exception $e) {
      $this->err="Error removing temporary files from $map_image_path: ".$e->getMessage();
      return;
    }
    if ($include_temporary_mapfiles) {
      // remove temporary map files
      try {
        $this->remove($map_path,$threshold);
      } catch (Exception $e) {
        $this->err="Error removing temporary files from $map_path: ".$e->getMessage();
        return;
      }
    }
  }
  
  private function remove($path,$threshold) {
    // convert threshold to seconds
    $threshold*=60;
    // get the current time
    $now=time();  // UNIX timestamp
    if (($dh=opendir($path))!==false) {
      while (($entry=readdir($dh))!==false) {
        if ($entry!="." && $entry!=".." && $entry!="index.php") {
          $entry_path=$path."/".$entry;
          if (is_file($entry_path)) {
            $file_info=stat($entry_path);
            $last_access=$file_info['atime'];
            if ($now-$last_access>$threshold) {
              // file is older than threshold seconds
              if (unlink($entry_path)===false) {
                throw new Exception("Unable to delete file '$entry' in $path");
              }
            }
            clearstatcache();
          }
        }
      }
      closedir($dh);
    } else {
      throw new Exception("Unable to open directory '$path'");
    }
  }
}
?>
