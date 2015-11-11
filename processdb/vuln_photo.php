<?php
// connect to the MIRISK database
  $conn = pg_connect("host=localhost port=5432 user=pgsql password=miriskdb dbname=MIRISK");

// get the large object id
  $id = $_GET["pid"];

Header("Content-type: image/jpeg");

// get the image and display
  pg_exec($conn,"begin");
  
  $fd = pg_lo_open($conn, $id,"r");
  pg_lo_read_all($fd);
  pg_lo_close($fd);

  pg_exec($conn,"END");  

  pg_close($conn);
?>
