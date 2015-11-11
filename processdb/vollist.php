<?php

// this file display the volcano.

Header("Content-type: image/jpeg");

$pid = $_GET["pid"];

// connect to the MIRISK database

  include "./connect_pg.php";

  @$conn = connect_pg("MIRISK");

  pg_exec($conn,"begin");
  
  $fd = pg_lo_open($conn, $pid,"r");
  pg_lo_read_all($fd);
  pg_lo_close($fd);

  pg_exec($conn,"end");  

  pg_close($conn);

?>
