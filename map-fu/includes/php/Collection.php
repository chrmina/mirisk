<?php
 /***********************************************************************
 * @file          Collection.php
 *
 * $Id: Collection.php 62 2006-11-14 22:10:14Z tim_j_welch $
 * $URL: https://svn.sourceforge.net/svnroot/map-fu/trunk/includes/php/Collection.php $
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
 * This file contains the class Collection. It is intended to be an object that
 * will hold and easily iterate over a collection of "things".    
 ***************************************************************************/

class Collection {
  private $items;
  
  function __construct() {
    $this->items=array();
  }
  
  function __destruct() {
    $this->destroy();
  }
  
  public function &GetFirstItem() {
    // returns false if the items array is empty
    return reset($this->items);
  }
  
  public function &GetLastItem() {
    // returns false if the items array is empty
    return end($this->items);
  }
  
  public function &GetNextItem() {
    // returns false if the next item is beyond the end of the array
    return next($this->items);
  }
  
  public function &GetPreviousItem() {
    // returns false if the previous item is beyond the beginning of the array
    return prev($this->items);
  }
  
  public function Exists($key) {
    return array_key_exists($key,$this->items);
  }
  
  public function CurrentKey() {
    return key($this->items);
  }
  
  public function &GetItem($key) {
    if (count($this->items)==0) {
      throw new Exception("Collection::GetItem: collection is empty");
    } elseif (strlen($key)>0) {
      if ($this->Exists($key))
        return $this->items[$key];
      else
        throw new Exception("Collection::GetItem: '$key' does not exist");
    } else {
      throw new Exception("Collection::GetItem: empty key");
    }
  }
  
  public function Keys() {
    return array_keys($this->items);
  }
  
  public function SetItem($key,$item) {
    if (count($this->items)==0) {
      throw new Exception("Collection::SetItem: collection is empty");
    } elseif (strlen($key)>0) {
      if ($this->Exists($key))
        $this->items[$key]=$item;
      else
        throw new Exception("Collection::SetItem: '$key' does not exist");
    } else
      throw new Exception("Collection::SetItem: empty key");
  }
  
  public function AddItem($key,$item) {
    if (strlen($key)>0) {
      if ($this->Exists($key))
        throw new Exception("Collection::AddItem: '$key' already exists");
      else
        $this->items[$key]=$item;
    } else
      throw new Exception("Collection::AddItem: empty key");
  }
  
  public function RemoveItem($key) {
    if (count($this->items)==0) {
      throw new Exception("Collection::RemoveItem: collection is empty");
    } elseif (strlen($key)>0) {
      if ($this->Exists($key))
        unset($this->items[$key]);
      else
        throw new Exception("Collection::RemoveItem: '$key' does not exist");
    } else
      throw new Exception("Collection::RemoveItem: empty key");
  }
  
  public function Clear() {
    $this->destroy();
  }
  
  public function Count() {
    return count($this->items);
  }
  
  private function destroy() {
    $cnt=count($this->items);
    for ($i=$cnt-1;$i>=0;--$i)
      unset($this->items[$i]);
    $this->items=array();
  }
}

?>
